<?php

class EcoController {
    private $db;
    private $ecoCalculator;

    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        require_once __DIR__ . '/../models/EcoCalculator.php';

        $database = new Database();
        $this->db = $database->getConnection();
        $this->ecoCalculator = new EcoCalculator($this->db);
    }

    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // Obtener estadísticas del usuario
        $stats = $this->ecoCalculator->getUserStats($user_id);
        $recent_scores = $this->ecoCalculator->getByUserId($user_id, 5);
        $monthly_stats = $this->ecoCalculator->getMonthlyStats($user_id);

        // Datos para gráficos
        $chart_data = [];
        $labels = [];

        foreach ($monthly_stats as $stat) {
            $labels[] = $stat['month'];
            $chart_data[] = round($stat['avg_co2'], 2);
        }

        // Calcular nivel ecológico
        $eco_level = $this->calculateEcoLevel($stats['avg_co2'] ?? 0);
        $achievements = $this->getAchievements($user_id, $stats);

        include __DIR__ . '/../views/dashboard.php';
    }

    public function results() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        if (!isset($_SESSION['last_result'])) {
            header('Location: index.php?page=dashboard');
            exit;
        }

        $result = $_SESSION['last_result'];
        unset($_SESSION['last_result']);

        // Comparar con promedio del usuario
        $stats = $this->ecoCalculator->getUserStats($_SESSION['user_id']);
        $user_avg = $stats['avg_co2'] ?? 0;

        $comparison = [
            'better_than_average' => $result['co2_kg'] < $user_avg,
            'percentage_difference' => $user_avg > 0 ? round((($user_avg - $result['co2_kg']) / $user_avg) * 100, 1) : 0
        ];

        include __DIR__ . '/../views/results.php';
    }

    public function achievements() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $stats = $this->ecoCalculator->getUserStats($user_id);
        $achievements = $this->getAchievements($user_id, $stats);
        $recent_scores = $this->ecoCalculator->getByUserId($user_id, 10);

        include __DIR__ . '/../views/achievements.php';
    }

    public function compare() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $stats = $this->ecoCalculator->getUserStats($user_id);
        $monthly_stats = $this->ecoCalculator->getMonthlyStats($user_id);

        // Referencias globales (valores promedio)
        $global_references = [
            'transport' => [
                'coche' => 4.6,
                'moto' => 2.0,
                'transporte público' => 1.0,
                'bicicleta' => 0.0,
                'a pie' => 0.0
            ],
            'diet' => [
                'carnívora' => 3.0,
                'mixta' => 1.5,
                'vegetariana' => 0.8
            ],
            'avg_spanish' => 7.5, // Promedio español kg CO2/día
            'recommended' => 4.0    // Recomendado ONU kg CO2/día
        ];

        include __DIR__ . '/../views/compare.php';
    }

    private function calculateEcoLevel($avg_co2) {
        if ($avg_co2 <= 3) {
            return [
                'level' => 'Eco Heroe',
                'color' => '#22c55e',
                'icon' => '🌟',
                'description' => '¡Eres un ejemplo de sostenibilidad!'
            ];
        } elseif ($avg_co2 <= 5) {
            return [
                'level' => 'Eco Consciente',
                'color' => '#84cc16',
                'icon' => '🌿',
                'description' => 'Vas por buen camino hacia la sostenibilidad'
            ];
        } elseif ($avg_co2 <= 7) {
            return [
                'level' => 'Eco Aprendiz',
                'color' => '#eab308',
                'icon' => '🌱',
                'description' => 'Estás empezando a ser más ecológico'
            ];
        } else {
            return [
                'level' => 'Eco Principiante',
                'color' => '#ef4444',
                'icon' => '🌍',
                'description' => 'Hay mucho margen de mejora'
            ];
        }
    }

    private function getAchievements($user_id, $stats) {
        $achievements = [];

        // Logros por cantidad de cálculos
        if ($stats['total_calculations'] >= 1) {
            $achievements[] = [
                'id' => 'first_calculation',
                'name' => 'Primer Paso',
                'description' => 'Realizaste tu primer cálculo de huella ecológica',
                'icon' => '👣',
                'unlocked' => true,
                'unlocked_at' => date('Y-m-d')
            ];
        }

        if ($stats['total_calculations'] >= 7) {
            $achievements[] = [
                'id' => 'week_warrior',
                'name' => 'Guerrero Semanal',
                'description' => 'Realizaste cálculos durante una semana completa',
                'icon' => '📅',
                'unlocked' => true,
                'unlocked_at' => date('Y-m-d')
            ];
        }

        if ($stats['total_calculations'] >= 30) {
            $achievements[] = [
                'id' => 'monthly_master',
                'name' => 'Maestro Mensual',
                'description' => 'Realizaste cálculos durante un mes completo',
                'icon' => '🏆',
                'unlocked' => true,
                'unlocked_at' => date('Y-m-d')
            ];
        }

        // Logros por huella baja
        if ($stats['min_co2'] <= 3) {
            $achievements[] = [
                'id' => 'eco_hero',
                'name' => 'Héroe Ecológico',
                'description' => 'Alcanzaste una huella de carbono inferior a 3 kg CO2/día',
                'icon' => '🦸‍♂️',
                'unlocked' => true,
                'unlocked_at' => date('Y-m-d')
            ];
        }

        // Logros por consistencia
        if ($stats['max_co2'] - $stats['min_co2'] <= 2 && $stats['total_calculations'] >= 5) {
            $achievements[] = [
                'id' => 'consistent_eco',
                'name' => 'Consistente Ecológico',
                'description' => 'Mantienes una huella de carbono estable y baja',
                'icon' => '⚖️',
                'unlocked' => true,
                'unlocked_at' => date('Y-m-d')
            ];
        }

        return $achievements;
    }

    public function exportData() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $scores = $this->ecoCalculator->getByUserId($user_id, 1000);
        $stats = $this->ecoCalculator->getUserStats($user_id);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="ecotrack_data_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // Cabeceras
        fputcsv($output, ['Fecha', 'CO2 (kg)', 'Consejos']);

        // Datos
        foreach ($scores as $score) {
            fputcsv($output, [
                $score['created_at'],
                $score['co2_kg'],
                $score['advice']
            ]);
        }

        // Resumen
        fputcsv($output, []);
        fputcsv($output, ['RESUMEN']);
        fputcsv($output, ['Promedio CO2', $stats['avg_co2'] ?? 0]);
        fputcsv($output, ['Mínimo CO2', $stats['min_co2'] ?? 0]);
        fputcsv($output, ['Máximo CO2', $stats['max_co2'] ?? 0]);
        fputcsv($output, ['Total cálculos', $stats['total_calculations'] ?? 0]);

        fclose($output);
        exit;
    }
}
?>
