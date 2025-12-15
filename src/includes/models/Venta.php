<?php
if (!class_exists('Venta')) {
class Venta {
    private $conn;
    private $table = "ventas";

    public $id_venta;
    public $id_vendedor;
    public $fecha;        // DATE o DATETIME (usa el tipo que tengas)
    public $monto;        // DECIMAL(10,2)
    public $metodo_pago;  // VARCHAR(20)
    public $nota;         // VARCHAR(255)

    // campos derivados para joins
    public $vendedor_nombre;

    public function __construct($db) { $this->conn = $db; }

    // Listar con join a vendedor
    public function leer() {
        $sql = "SELECT v.id_venta, v.id_vendedor, ven.vendedor AS vendedor_nombre,
                       v.fecha, v.monto, v.metodo_pago, v.nota
                FROM {$this->table} v
                INNER JOIN vendedor ven ON ven.id = v.id_vendedor
                ORDER BY v.fecha DESC, v.id_venta DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    // Leer uno
    public function leerUno() {
        $sql = "SELECT v.id_venta, v.id_vendedor, ven.vendedor AS vendedor_nombre,
                       v.fecha, v.monto, v.metodo_pago, v.nota
                FROM {$this->table} v
                INNER JOIN vendedor ven ON ven.id = v.id_vendedor
                WHERE v.id_venta = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $this->id_venta, PDO::PARAM_INT);
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->id_vendedor   = (int)$row['id_vendedor'];
            $this->vendedor_nombre = $row['vendedor_nombre'];
            $this->fecha         = $row['fecha'];
            $this->monto         = $row['monto'];
            $this->metodo_pago   = $row['metodo_pago'];
            $this->nota          = $row['nota'];
            return true;
        }
        return false;
    }

    // Crear
    public function crear() {
        $sql = "INSERT INTO {$this->table}
                   (id_vendedor, fecha, monto, metodo_pago, nota)
                VALUES (:id_vendedor, :fecha, :monto, :metodo_pago, :nota)";
        $stmt = $this->conn->prepare($sql);

        $this->id_vendedor = (int)$this->id_vendedor;
        $this->fecha       = htmlspecialchars(strip_tags($this->fecha));
        $this->monto       = (string)$this->monto;
        $this->metodo_pago = htmlspecialchars(strip_tags($this->metodo_pago));
        $this->nota        = htmlspecialchars(strip_tags($this->nota));

        $stmt->bindParam(':id_vendedor', $this->id_vendedor, PDO::PARAM_INT);
        $stmt->bindParam(':fecha',       $this->fecha);
        $stmt->bindParam(':monto',       $this->monto);
        $stmt->bindParam(':metodo_pago', $this->metodo_pago);
        $stmt->bindParam(':nota',        $this->nota);
        return $stmt->execute();
    }

    // Actualizar
    public function actualizar() {
        $sql = "UPDATE {$this->table}
                   SET id_vendedor=:id_vendedor, fecha=:fecha, monto=:monto,
                       metodo_pago=:metodo_pago, nota=:nota
                 WHERE id_venta=:id_venta";
        $stmt = $this->conn->prepare($sql);

        $this->id_venta    = (int)$this->id_venta;
        $this->id_vendedor = (int)$this->id_vendedor;
        $this->fecha       = htmlspecialchars(strip_tags($this->fecha));
        $this->monto       = (string)$this->monto;
        $this->metodo_pago = htmlspecialchars(strip_tags($this->metodo_pago));
        $this->nota        = htmlspecialchars(strip_tags($this->nota));

        $stmt->bindParam(':id_venta',    $this->id_venta,    PDO::PARAM_INT);
        $stmt->bindParam(':id_vendedor', $this->id_vendedor, PDO::PARAM_INT);
        $stmt->bindParam(':fecha',       $this->fecha);
        $stmt->bindParam(':monto',       $this->monto);
        $stmt->bindParam(':metodo_pago', $this->metodo_pago);
        $stmt->bindParam(':nota',        $this->nota);
        return $stmt->execute();
    }

    // Eliminar
    public function eliminar() {
        $sql = "DELETE FROM {$this->table} WHERE id_venta = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $this->id_venta, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Contar
    public function contar() {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$r['total'];
    }
}
}
