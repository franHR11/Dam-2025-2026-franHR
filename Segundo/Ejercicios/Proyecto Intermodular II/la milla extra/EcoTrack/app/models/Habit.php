<?php

class Habit {
    private $conn;
    private $table_name = "habits";

    public $id;
    public $user_id;
    public $transport;
    public $energy_use;
    public $diet_type;
    public $recycling;
    public $date_recorded;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET user_id=:user_id, transport=:transport, energy_use=:energy_use,
                      diet_type=:diet_type, recycling=:recycling, date_recorded=NOW()";

        $stmt = $this->conn->prepare($query);

        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->transport = htmlspecialchars(strip_tags($this->transport));
        $this->energy_use = htmlspecialchars(strip_tags($this->energy_use));
        $this->diet_type = htmlspecialchars(strip_tags($this->diet_type));
        $this->recycling = htmlspecialchars(strip_tags($this->recycling));

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":transport", $this->transport);
        $stmt->bindParam(":energy_use", $this->energy_use);
        $stmt->bindParam(":diet_type", $this->diet_type);
        $stmt->bindParam(":recycling", $this->recycling);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE user_id = ? ORDER BY date_recorded DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE user_id = ? ORDER BY date_recorded DESC LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->transport = $row['transport'];
            $this->energy_use = $row['energy_use'];
            $this->diet_type = $row['diet_type'];
            $this->recycling = $row['recycling'];
            $this->date_recorded = $row['date_recorded'];
            return true;
        }

        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
