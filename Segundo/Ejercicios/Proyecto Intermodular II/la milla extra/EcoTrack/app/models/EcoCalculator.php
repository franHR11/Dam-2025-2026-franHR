<?php

class EcoCalculator {
    private $conn;
    private $table_name = "eco_scores";

    public $id;
    public $user_id;
    public $co2_kg;
    public $advice;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function calculateCO2($transport, $energy, $diet, $recycle) {
        $co2 = 0;

        // Transporte CO2 kg por día
        switch($transport) {
            case 'coche':
                $co2 += 4.6;
                break;
            case 'moto':
                $co2 += 2.0;
                break;
            case 'transporte público':
                $co2 += 1.0;
                break;
            case 'bicicleta':
            case 'a pie':
                $co2 += 0.0;
                break;
        }

        // Energía eléctrica (kWh mensual convertido a diario)
        $daily_energy = $energy / 30;
        $co2 += $daily_energy * 0.233;

        // Dieta CO2 kg por día
        switch($diet) {
            case 'carnívora':
                $co2 += 3.0;
                break;
            case 'mixta':
                $co2 += 1.5;
                break;
            case 'vegetariana':
                $co2 += 0.8;
                break;
        }

        // Reciclaje reduce CO2
        if ($recycle) {
            $co2 -= 0.5;
        }

        return max($co2, 0);
    }

    public function generateAdvice($co2_kg) {
        $advice = [];

        if ($co2_kg > 8) {
            $advice[] = "Tu huella de carbono es muy alta. Considera usar más transporte público o bicicleta.";
            $advice[] = "Reduce tu consumo de carne para disminuir significativamente tu impacto ambiental.";
            $advice[] = "Asegúrate de reciclar correctamente todos tus residuos.";
        } elseif ($co2_kg > 5) {
            $advice[] = "Tu huella de carbono es moderada. Pequeños cambios pueden marcar la diferencia.";
            $advice[] = "Intenta combinar diferentes medios de transporte más sostenibles.";
            $advice[] = "Considera incluir más días vegetarianos en tu dieta.";
        } elseif ($co2_kg > 3) {
            $advice[] = "¡Buen trabajo! Tu huella de carbono es relativamente baja.";
            $advice[] = "Sigue manteniendo tus hábitos sostenibles.";
            $advice[] = "Comparte tus consejos con amigos y familiares.";
        } else {
            $advice[] = "¡Excelente! Tienes una huella de carbono muy baja.";
            $advice[] = "Eres un ejemplo de sostenibilidad ambiental.";
            $advice[] = "Considera participar en iniciativas ecológicas locales.";
        }

        return implode(" ", $advice);
    }

    public function saveScore($user_id, $co2_kg, $advice) {
        $query = "INSERT INTO " . $this->table_name . "
                  SET user_id=:user_id, co2_kg=:co2_kg, advice=:advice, created_at=NOW()";

        $stmt = $this->conn->prepare($query);

        $this->user_id = htmlspecialchars(strip_tags($user_id));
        $this->co2_kg = htmlspecialchars(strip_tags($co2_kg));
        $this->advice = htmlspecialchars(strip_tags($advice));

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":co2_kg", $this->co2_kg);
        $stmt->bindParam(":advice", $this->advice);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    public function getByUserId($user_id, $limit = 10) {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserStats($user_id) {
        $query = "SELECT
                    AVG(co2_kg) as avg_co2,
                    MIN(co2_kg) as min_co2,
                    MAX(co2_kg) as max_co2,
                    COUNT(*) as total_calculations
                  FROM " . $this->table_name . "
                  WHERE user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMonthlyStats($user_id) {
        $query = "SELECT
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    AVG(co2_kg) as avg_co2,
                    COUNT(*) as calculations
                  FROM " . $this->table_name . "
                  WHERE user_id = ?
                  GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                  ORDER BY month DESC
                  LIMIT 12";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
