<?php

class HabitController {
    private $db;
    private $habit;
    private $ecoCalculator;

    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        require_once __DIR__ . '/../models/Habit.php';
        require_once __DIR__ . '/../models/EcoCalculator.php';

        $database = new Database();
        $this->db = $database->getConnection();
        $this->habit = new Habit($this->db);
        $this->ecoCalculator = new EcoCalculator($this->db);
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $habits = $this->habit->getByUserId($_SESSION['user_id']);
        include __DIR__ . '/../views/habits.php';
    }

    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->habit->user_id = $_SESSION['user_id'];
            $this->habit->transport = $_POST['transport'] ?? '';
            $this->habit->energy_use = $_POST['energy_use'] ?? 0;
            $this->habit->diet_type = $_POST['diet_type'] ?? '';
            $this->habit->recycling = isset($_POST['recycling']) ? 1 : 0;

            // Validaciones
            $valid_transports = ['coche', 'moto', 'transporte público', 'bicicleta', 'a pie'];
            $valid_diets = ['vegetariana', 'mixta', 'carnívora'];

            if (!in_array($this->habit->transport, $valid_transports)) {
                $_SESSION['error'] = "Transporte no válido";
                include __DIR__ . '/../views/habit_form.php';
                return;
            }

            if (!is_numeric($this->habit->energy_use) || $this->habit->energy_use < 0) {
                $_SESSION['error'] = "El consumo de energía debe ser un número positivo";
                include __DIR__ . '/../views/habit_form.php';
                return;
            }

            if (!in_array($this->habit->diet_type, $valid_diets)) {
                $_SESSION['error'] = "Tipo de dieta no válido";
                include __DIR__ . '/../views/habit_form.php';
                return;
            }

            if ($this->habit->create()) {
                // Calcular CO2
                $co2_kg = $this->ecoCalculator->calculateCO2(
                    $this->habit->transport,
                    $this->habit->energy_use,
                    $this->habit->diet_type,
                    $this->habit->recycling
                );

                // Generar consejos
                $advice = $this->ecoCalculator->generateAdvice($co2_kg);

                // Guardar resultado
                $this->ecoCalculator->saveScore($_SESSION['user_id'], $co2_kg, $advice);

                $_SESSION['success'] = "Hábito registrado correctamente";
                $_SESSION['last_result'] = [
                    'co2_kg' => $co2_kg,
                    'advice' => $advice
                ];

                header('Location: index.php?page=results');
                exit;
            } else {
                $_SESSION['error'] = "Error al registrar el hábito";
                include __DIR__ . '/../views/habit_form.php';
                return;
            }
        }

        include __DIR__ . '/../views/habit_form.php';
    }

    public function delete($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        // Verificar que el hábito pertenece al usuario actual
        $habits = $this->habit->getByUserId($_SESSION['user_id']);
        $habit_belongs_to_user = false;

        foreach ($habits as $habit) {
            if ($habit['id'] == $id) {
                $habit_belongs_to_user = true;
                break;
            }
        }

        if (!$habit_belongs_to_user) {
            $_SESSION['error'] = "No tienes permiso para eliminar este hábito";
            header('Location: index.php?page=habits');
            exit;
        }

        if ($this->habit->delete($id)) {
            $_SESSION['success'] = "Hábito eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar el hábito";
        }

        header('Location: index.php?page=habits');
        exit;
    }

    public function history() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $habits = $this->habit->getByUserId($_SESSION['user_id']);
        $eco_scores = $this->ecoCalculator->getByUserId($_SESSION['user_id']);
        $stats = $this->ecoCalculator->getUserStats($_SESSION['user_id']);
        $monthly_stats = $this->ecoCalculator->getMonthlyStats($_SESSION['user_id']);

        include __DIR__ . '/../views/history.php';
    }
}
?>
