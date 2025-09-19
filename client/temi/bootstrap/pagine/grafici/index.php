<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grafico Elezioni Orizzontale</title>

    <!-- Bootstrap Italia CSS -->
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-italia/dist/css/bootstrap-italia.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        #chartContainer {
            width: 400px;  /* Larghezza ridotta */
            height: 400px; /* Altezza aumentata per l'orizzontalità */
            margin: 0 auto; /* Centra il grafico */
        }
    </style>
</head>
<body>

    <nav class="it-navbar-wrapper">
        <div class="it-navbar">
            <a href="#" class="navbar-brand">Sito Elezioni</a>
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="#">Grafici</a></li>
            </ul>
        </div>
    </nav>

    <main class="container mt-5">
        <h1 class="text-center mb-4">Risultati Elettorali - Grafico Orizzontale</h1>

        <div class="card p-4">
            <h3 class="mb-3">Voti per Partito (Grafico Orizzontale)</h3>
            <div id="chartContainer">
                <canvas id="votiChart"></canvas> <!-- Canvas per il grafico -->
            </div>
        </div>
    </main>

    <footer class="text-center mt-5">
        <p>© 2025 - Sito Elezioni conforme a Bootstrap Italia</p>
    </footer>

    <!-- Script per il grafico -->
    <script>
        var ctx = document.getElementById('votiChart').getContext('2d');
        var votiChart = new Chart(ctx, {
            type: 'bar',  // Grafico a barre orizzontali (aggiornato)
            data: {
                labels: ['Partito A', 'Partito B', 'Partito C', 'Partito D'],
                datasets: [{
                    label: 'Voti ricevuti',
                    data: [1200, 1900, 1500, 1000],
                    backgroundColor: ['rgba(54, 162, 235, 0.6)', 'rgba(255, 99, 132, 0.6)', 'rgba(255, 206, 86, 0.6)', 'rgba(75, 192, 192, 0.6)'],
                    borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',  // CAMBIA a barre ORIZZONTALI
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <!-- Bootstrap Italia JS -->
    <script src="https://unpkg.com/bootstrap-italia/dist/js/bootstrap-italia.bundle.min.js"></script>

</body>
</html>
