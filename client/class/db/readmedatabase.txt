

Nel file config.php, aggiungere la variabile:
$dbtype = "MySQL"; 
oppure 
mysql4
sqlite
mssql
oracle
msaccess
mssql-odbc
db2
postgres

Caricare poi il file db.php :


@require_once(config.php");
@require_once(db/db.php");

ed usare cosi'
$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_config"));


sostituendo, per esmpio, mysql_query con $db->sql_query 
e cosi' via.
Ecco i comandi sql supportati

$db->sql_query
$db->sql_numrows
$db->sql_affectedrows
$db->sql_numfields
$db->sql_fieldname
$db->sql_fieldtype
$db->sql_fetchrow
$db->sql_fetchrowset
$db->sql_fetchfield
$db->sql_rowseek
$db->sql_nextid
$db->sql_freeresult
$db->sql_error

