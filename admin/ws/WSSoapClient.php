<?php
if (!defined('ADMIN_FILE')) {
    die ("You can't access this file directly...");
}

class WSSoapClient extends SoapClient {
    private $p12File;
    private $p12Password;
    private $privateKeyId;
    private $publicCert;
    private $NS = array(
        'com' => 'http://xsd.ws.siel.mininterno.it/common',
        'wsse' => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd',
        'wsu' => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd',
        'soapenv' => 'http://schemas.xmlsoap.org/soap/envelope/',
        'xsd' => 'http://xsd.ws.siel.mininterno.it/',
        'encType' => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary',
        'ds' => 'http://www.w3.org/2000/09/xmldsig#',
        'dsSha' => 'http://www.w3.org/2000/09/xmldsig#sha1',
        'ec' => 'http://www.w3.org/2001/10/xml-exc-c14n#',
        'x509Token' => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-token-profile-1.0#X509v3',
        'signMeth' => 'http://www.w3.org/2000/09/xmldsig#rsa-sha1',
        'secext1' => 'http://docs.oasis-open.org/wss/oasis-wss-wssecurity-secext-1.1.xsd'
    );
    private $doc;

    public function __construct($wsdl, $options, $p12File, $p12Password) {
        parent::__construct($wsdl, $options);
        $this->p12File = $p12File;
        $this->p12Password = $p12Password;

        $certs = [];
        if (!openssl_pkcs12_read(file_get_contents( $p12File ), $certs, $p12Password)) {
            throw new Exception("Unable to read .p12 certificate");
        }

        $this->privateKeyId = openssl_pkey_get_private($certs['pkey']);
        $this->publicCert = $certs['cert'];

        $this->publicCert = str_replace('-----BEGIN CERTIFICATE-----', '', $this->publicCert);
        $this->publicCert = str_replace('-----END CERTIFICATE-----', '', $this->publicCert);
        $this->publicCert = str_replace("\n", "", $this->publicCert);
        $this->publicCert = str_replace(" ", "", $this->publicCert);

        $this->doc = new DOMDocument();
    }

    private function fixNamespaces($request) {
        $xpath = new DOMXPath($this->doc);
        $xpath->registerNamespace('com', 'http://xsd.ws.siel.mininterno.it/common');

        // Rinomina i prefissi secondo la mappa
        foreach ($this->NS as $prefix => $uri) {
            $nodes = $xpath->query("//*[namespace-uri() = '$uri']");
            if($nodes->length>0){
                $request = preg_replace('/xmlns:'.$nodes->item(0)->prefix.'/', 'xmlns:'.$prefix, $request);
                $request = preg_replace('/<'.$nodes->item(0)->prefix.':(\w+)/', '<'.$prefix.':$1', $request);
                $request = preg_replace('/\/'.$nodes->item(0)->prefix.':(\w+)/', '/'.$prefix.':$1', $request);
            }   
        }

        // Salva l'XML modificato
        $this->doc->loadXML($request);
        return $request;
    }

    public function __doRequest($request, $location, $action, $version, $one_way = 0) {
        $this->doc->loadXML($request);
        $request = $this->fixNamespaces($request);

 
        $request=$this->addWSSecurityHeader();
//        echo "<br><br>REQUEST: ".htmlentities($request)."<br><br>";

        //preventDefault
//        return 0;
        //for future
        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }

    private function getUUID($data=null) {
        if ($data === null)
            $data = microtime() . uniqid();
        $id = md5($data);
        return sprintf('%08s-%04s-%04s-%04s-%012s', substr($id, 0, 8), substr($id, 8, 4), substr($id, 12, 4), substr(16, 4), substr($id, 20));
    }

    private function creadigest($obj) {
        if(is_string($obj))
            $string = $obj;
        else
            $string = rtrim($this->doc->saveXML($obj));

        return base64_encode(sha1($string, true));
    }

    private function timestamp($tsid) {
        $date = new DateTime('now', new DateTimeZone('Europe/Rome'));
        $date2 = new DateTime('now + 10 second', new DateTimeZone('Europe/Rome'));
        $tm_created = $date->format('Y-m-d\TG:i:s.').substr(gettimeofday()["usec"],0,3)."Z";
        $tm_expires = $date2->format('Y-m-d\TG:i:s.').substr(gettimeofday()["usec"],0,3)."Z";

        $timestamp = $this->doc->createElement('wsu:Timestamp');

        //append attribute id to timestamp
        $wsuId = $this->doc->createAttribute('wsu:Id');
        $wsuId->value = $tsid;
        $timestamp->appendChild($wsuId);

        //append element created to timestamp
        $created = $this->doc->createElement('wsu:Created',$tm_created);
        $timestamp->appendChild($created);

        //append element expires to timestamp
        $expires = $this->doc->createElement('wsu:Expires',$tm_expires);
        $timestamp->appendChild($expires);

        return $timestamp;
    }

    private function binarySecurityToken($bstid) {
        $bst = $this->doc->createElement('wsse:BinarySecurityToken', $this->publicCert);

        //append attribute encodingType to bst
        $encodingType = $this->doc->createAttribute('EncodingType');
        $encodingType->value = $this->NS["encType"];
        $bst->appendChild($encodingType);

        //append attribute valueType to bst
        $valueType = $this->doc->createAttribute('ValueType');
        $valueType->value = $this->NS["x509Token"];
        $bst->appendChild($valueType);

        //append attribute id to bst
        $wsuId = $this->doc->createAttribute('wsu:Id');
        $wsuId->value = $bstid;
        $bst->appendChild($wsuId);
        
        return $bst;
    }

    private function body($obj,$bodyid) {
        //append attribute id to body
        $wsuId = $this->doc->createAttribute('wsu:Id');
        $wsuId->value = $bodyid;
        $obj->appendChild($wsuId);

        //append wsu namespace
        $wsuNs = $this->doc->createAttribute('xmlns:wsu');
        $wsuNs->value = $this->NS['wsu'];
        $obj->appendChild($wsuNs);
        
        return $obj;
    }

    private function reference($digest,$tag,$id) {
        $reference = $this->doc->createElement('ds:Reference');

        //append attribute uri to reference
        $uri = $this->doc->createAttribute('URI');
        $uri->value = '#'.$id;
        $reference->appendChild($uri);


        //==== trasforms ====
        //create element transforms
        $transforms = $this->doc->createElement('ds:Transforms');

        //append element transform to transforms
        $transform = $this->doc->createElement('ds:Transform');
        
        //append attribute algorithm to transform
        $algorithm = $this->doc->createAttribute('Algorithm');
        $algorithm->value = $this->NS['ec'];
        $transform->appendChild($algorithm);

        //create element inclusiveNamespaces
        $inclusiveNamespaces = $this->doc->createElement('ec:InclusiveNamespaces');

        //append namespaces to inclusiveNamespaces
        $ecNs = $this->doc->createAttribute('xmlns:ec');
        $ecNs->value = $this->NS['ec'];
        $inclusiveNamespaces->appendChild($ecNs);

        //append attribute prefixList to inclusiveNamespaces
        $prefixList = $this->doc->createAttribute('PrefixList');
        if($tag=='Timestamp')
            $prefix = 'wsse com soapenv xsd';
        elseif($tag=='BinarySecurityToken')
            $prefix = '';
        elseif($tag=='Body')
            $prefix = 'com xsd';
        $prefixList->value = $prefix;
        $inclusiveNamespaces->appendChild($prefixList);

        //append element inclusiveNamespaces to transform
        $transform->appendChild($inclusiveNamespaces);
        //append element transform to trasforms
        $transforms->appendChild($transform);
        //append element transforms to reference
        $reference->appendChild($transforms);


        //==== digestMethod ====
        //create element digestMethod
        $digestMethod = $this->doc->createElement('ds:DigestMethod');

        //append attribute algorithm to digestMethod
        $algorithm = $this->doc->createAttribute('Algorithm');
        $algorithm->value = $this->NS['dsSha'];
        $digestMethod->appendChild($algorithm);

        //append element digestMethod to reference
        $reference->appendChild($digestMethod);


        //==== digestValue ====
        //append element digestValue to reference
        $digestValue = $this->doc->createElement('ds:DigestValue',$digest);        
        $reference->appendChild($digestValue);

        return $reference;
    }

    private function addNamespaces($xml) {
        $obj = $xml->cloneNode(true);
        $firstAttr = $obj->attributes->item(0);

        switch($obj->nodeName) {
            case 'wsu:Timestamp':
                //add namespaces to timestamp
                //com
                $comNs = $this->doc->createAttribute('xmlns:com');
                $comNs->value = $this->NS['com'];
                $obj->insertBefore($comNs,$firstAttr);
                //soapenv
                $soapenvNs = $this->doc->createAttribute('xmlns:soapenv');
                $soapenvNs->value = $this->NS['soapenv'];
                $obj->insertBefore($soapenvNs,$firstAttr);
                //wsse
                $wsseNs = $this->doc->createAttribute('xmlns:wsse');
                $wsseNs->value = $this->NS['wsse'];
                $obj->insertBefore($wsseNs,$firstAttr);
                //wsu
                $wsuNs = $this->doc->createAttribute('xmlns:wsu');
                $wsuNs->value = $this->NS['wsu'];
                $obj->insertBefore($wsuNs,$firstAttr);
                //xsd
                $xsdNs = $this->doc->createAttribute('xmlns:xsd');
                $xsdNs->value = $this->NS['xsd'];
                $obj->insertBefore($xsdNs,$firstAttr);
            break;
            case 'wsse:BinarySecurityToken':
                //add namespaces to bst
                //wsse
                $wsseNs = $this->doc->createAttribute('xmlns:wsse');
                $wsseNs->value = $this->NS['wsse'];
                $obj->insertBefore($wsseNs,$firstAttr);
                //wsu
                $wsuNs = $this->doc->createAttribute('xmlns:wsu');
                $wsuNs->value = $this->NS['wsu'];
                $obj->insertBefore($wsuNs,$firstAttr);
            break;
            case 'soapenv:Body':
                $obj2 = new DOMDocument();
                $obj2->loadXML($this->doc->saveXML());
                $xpath = new DOMXpath($obj2);
                //com
                $xpath->registerNamespace('com',$this->NS['com']);
                //soapenv
                $xpath->registerNamespace('soapenv',$this->NS['soapenv']);
                //xsd
                $xpath->registerNamespace('xsd', $this->NS['xsd']);
                //wsu
                $xpath->registerNamespace('wsu',$this->NS['wsu']);

                $elements = $xpath->query("//soapenv:Body");
                $obj = $elements->item(0)->C14N();
            break;
        }
        
        return $obj;
    }

     private function addDsNamespaces($xml) {
        $obj2 = new DOMDocument();
        $obj2->loadXML($this->doc->saveXML($xml));
        $xpath = new DOMXpath($obj2);

        //com
        $xpath->registerNamespace('com',$this->NS['com']);
        //ds
        $xpath->registerNamespace('ds',$this->NS['ds']);
        //soapenv
        $xpath->registerNamespace('soapenv',$this->NS['soapenv']);
        //xsd
        $xpath->registerNamespace('xsd', $this->NS['xsd']);


        $elements = $xpath->query("//ds:SignedInfo");
        $stringasig=$elements->item(0)->C14N();
		$aggiunginssign=' xmlns:com="http://xsd.ws.siel.mininterno.it/common" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://xsd.ws.siel.mininterno.it/"';
		$sigtesto=substr_replace($stringasig, $aggiunginssign, 14, 46);
        return $sigtesto;
        

/*
        //add namespaces to signedInfo
        //com
        $comNs = $this->doc->createAttribute('xmlns:com');
        $comNs->value = $this->NS['com'];
        $obj->insertBefore($comNs,$firstAttr);
        //ds
        $comDs = $this->doc->createAttribute('xmlns:ds');
        $comDs->value = $this->NS['ds'];
        $obj->insertBefore($comDs,$firstAttr);
        //soapenv
        $soapenvNs = $this->doc->createAttribute('xmlns:soapenv');
        $soapenvNs->value = $this->NS['soapenv'];
        $obj->insertBefore($soapenvNs,$firstAttr);
        //xsd
        $xsdNs = $this->doc->createAttribute('xmlns:xsd');
        $xsdNs->value = $this->NS['xsd'];
        $obj->insertBefore($xsdNs,$firstAttr);
*/
     }

    private function security($bst,$timestamp,$sigid,$rts,$rbst,$rbody,$keyid,$strid,$bstid) {
        $header = $this->doc->createElement('soapenv:Header');

        //create element security
        $security = $this->doc->createElement('wsse:Security');
        //append namespaces to security
        //wsse
        $wsseNs = $this->doc->createAttribute('xmlns:wsse');
        $wsseNs->value = $this->NS['wsse'];
        $security->appendChild($wsseNs);
        //wsu
        $wsuNs = $this->doc->createAttribute('xmlns:wsu');
        $wsuNs->value = $this->NS['wsu'];
        $security->appendChild($wsuNs);

        //append binary token to security
        $security->appendChild($bst);

        //create element signature
        $signature = $this->doc->createElement('ds:Signature');
        //append attribute id to signature
        $Id = $this->doc->createAttribute('Id');
        $Id->value = $sigid;
        $signature->appendChild($Id);
        //append namespaces to signature
        //ds
        $dsNs = $this->doc->createAttribute('xmlns:ds');
        $dsNs->value = $this->NS['ds'];
        $signature->appendChild($dsNs);

        //create element signedInfo
        $signedInfo = $this->doc->createElement('ds:SignedInfo');

        //create element canonicalizationMethod
        $canonicalizationMethod = $this->doc->createElement('ds:CanonicalizationMethod');
        //append attribute algorithm to canonicalizationMethod
        $algorithm = $this->doc->createAttribute('Algorithm');
        $algorithm->value = $this->NS['ec'];
        $canonicalizationMethod->appendChild($algorithm);

        //create element inclusiveNamespaces
        $inclusiveNamespaces = $this->doc->createElement('ec:InclusiveNamespaces');
        //append namespaces to inclusiveNamespaces
        $ecNs = $this->doc->createAttribute('xmlns:ec');
        $ecNs->value = $this->NS['ec'];
        $inclusiveNamespaces->appendChild($ecNs);
        //append attribute prefixList to inclusiveNamespaces
        $prefixList = $this->doc->createAttribute('PrefixList');
        $prefixList->value = 'com soapenv xsd';
        $inclusiveNamespaces->appendChild($prefixList);
        //append element inclusiveNamespaces to canonicalizationMethod 
        $canonicalizationMethod->appendChild($inclusiveNamespaces);

        //append element canonicalizationMethod to signedInfo
        $signedInfo->appendChild($canonicalizationMethod);

        //create element signatureMethod
        $signatureMethod = $this->doc->createElement('ds:SignatureMethod');
        //append attribute algorithm to signatureMethod
        $algorithm = $this->doc->createAttribute('Algorithm');
        $algorithm->value = $this->NS['signMeth'];
        $signatureMethod->appendChild($algorithm);
        //append element signatureMethod to signedInfo
        $signedInfo->appendChild($signatureMethod);

        //append generated references
        $signedInfo->appendChild($rts);
        $signedInfo->appendChild($rbst);
        $signedInfo->appendChild($rbody);
        
        //append element signedInfo to signature
        $signature->appendChild($signedInfo);

        //append element signatureValue to signature
        openssl_sign($this->addDsNamespaces($signature), $signed, $this->privateKeyId);
        //openssl_sign($this->doc->saveXML($this->addNamespaces($signedInfo)), $signed, $this->privateKeyId);
        
        $signatureValue = $this->doc->createElement('ds:SignatureValue',base64_encode($signed));
        //append element signedInfo to signature
        $signature->appendChild($signatureValue);

        //create element keyInfo
        $keyInfo = $this->doc->createElement('ds:KeyInfo');
        //append attribute Id to keyInfo
        $Id = $this->doc->createAttribute('Id');
        $Id->value = $keyid;
        $keyInfo->appendChild($Id);

        //create element securityTokenReference
        $securityTokenReference = $this->doc->createElement('wsse:SecurityTokenReference');
        //append attribute tokenType to securityTokenReference
        $tokenType = $this->doc->createAttribute('wsse11:TokenType');
        $tokenType->value = $this->NS['x509Token'];
        $securityTokenReference->appendChild($tokenType);
        //append attribute id to securityTokenReference
        $wsuId = $this->doc->createAttribute('wsu:Id');
        $wsuId->value = $strid;
        $securityTokenReference->appendChild($wsuId);
        //append namespaces to securityTokenReference
        $wsse11Ns = $this->doc->createAttribute('xmlns:wsse11');
        $wsse11Ns->value = $this->NS['secext1'];
        $securityTokenReference->appendChild($wsse11Ns);

        //create element reference
        $reference = $this->doc->createElement('wsse:Reference');
        //append attribute uri to reference
        $uri = $this->doc->createAttribute('URI');
        $uri->value = '#'.$bstid;
        $reference->appendChild($uri);
        //append attribute valueType to reference
        $valueType = $this->doc->createAttribute('ValueType');
        $valueType->value = $this->NS['x509Token'];
        $reference->appendChild($valueType);
        //append element reference to securityTokenReference
        $securityTokenReference->appendChild($reference);

        //append element securityTokenReference to keyInfo
        $keyInfo->appendChild($securityTokenReference);

        //append element keyInfo to signature
        $signature->appendChild($keyInfo);
        
        //append element signature to security
        $security->appendChild($signature);

        //append timestamp to security
        $security->appendChild($timestamp);
        
        //append element security to header
        $header->appendChild($security);
        return $header;
    }

    private function addWSSecurityHeader() {
        #memorizzazione degli ID in variabili
        $bodyid='id-'.$this->getUUID();
        $bstid='X509-'.$this->getUUID();
        $tsid='TS-'.$this->getUUID();
        $strid='STR-'.$this->getUUID(); 
        $keyid='KI-'.$this->getUUID(); 
        $sigid='SIG-'.$this->getUUID();

        #Timestamp
        $timestamp = $this->timestamp($tsid);        
        $timedigest=$this->creadigest($this->addNamespaces($timestamp));
        $rts = $this->reference($timedigest,'Timestamp',$tsid); 

        #BinarySecurityToken
        $bst = $this->binarySecurityToken($bstid);
        $bstdigest=$this->creadigest($this->addNamespaces($bst));
        $rbst = $this->reference($bstdigest,'BinarySecurityToken',$bstid); 

        #Body
        $bodyPos = $this->doc->getElementsByTagName('Body')->item(0);
        $body = $this->body($bodyPos,$bodyid);
        $bodydigest=$this->creadigest($this->addNamespaces($body));
        $rbody = $this->reference($bodydigest,'Body',$bodyid); 
        
        $this->doc->documentElement->insertBefore($this->security($bst,$timestamp,$sigid,$rts,$rbst,$rbody,$keyid,$strid,$bstid),$bodyPos);

        echo "<hr>";
 #       echo htmlspecialchars($this->doc->saveXML()); die();
return ($this->doc->saveXML());
    }
}
?>