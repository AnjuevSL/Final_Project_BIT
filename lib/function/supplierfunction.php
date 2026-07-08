<?php
include_once('main.php');
include_once('auto_id.php');

class Supplier extends Main {

    // CREATE — add a new supplier
    public function addsupplier($supplierName, $email, $phone, $address)
    {
        $autonumber = new AutoNumber;
        $id = $autonumber->NumberGenaration("supplierid", "suppliers_tbl", "SUP");

        $sql = $this->dbResult->prepare(
            "INSERT INTO suppliers_tbl (supplierid, supplierName, email, phone, address, d_status)
             VALUES (?, ?, ?, ?, ?, 1)"
        );
        $sql->bind_param("sssss", $id, $supplierName, $email, $phone, $address);

        if ($sql->execute()) {
            return "success";
        } else {
            return "error";
        }
    }

    // UPDATE — edit an existing supplier
    public function updatesupplier($supplierId, $supplierName, $email, $phone, $address)
    {
        $sql = $this->dbResult->prepare(
            "UPDATE suppliers_tbl
             SET supplierName = ?, email = ?, phone = ?, address = ?
             WHERE supplierid = ?"
        );
        $sql->bind_param("sssss", $supplierName, $email, $phone, $address, $supplierId);

        if ($sql->execute()) {
            return "success";
        } else {
            return "error";
        }
    }

    // TOGGLE STATUS — activate or deactivate
    public function toggleStatus($supplierId, $status)
    {
        $sql = $this->dbResult->prepare("UPDATE suppliers_tbl SET d_status = ? WHERE supplierid = ?");
        $sql->bind_param("is", $status, $supplierId);

        if ($sql->execute()) {
            return "success";
        } else {
            return "error";
        }
    }

    // DELETE — hard delete
    public function deletesupplier($supplierId)
    {
        $sql = $this->dbResult->prepare("DELETE FROM suppliers_tbl WHERE supplierid = ?");
        $sql->bind_param("s", $supplierId);

        if ($sql->execute()) {
            return "success";
        } else {
            return "error";
        }
    }

    // READ — all suppliers
    public function getAllSuppliers()
    {
        $sql = $this->dbResult->prepare(
            "SELECT supplierid, supplierName, email, phone, address, d_status
             FROM suppliers_tbl
             ORDER BY supplierid DESC"
        );
        $sql->execute();
        $result = $sql->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // READ — active suppliers only
    public function getActiveSuppliers()
    {
        $sql = $this->dbResult->prepare(
            "SELECT supplierid, supplierName
             FROM suppliers_tbl
             WHERE d_status = 1
             ORDER BY supplierName ASC"
        );
        $sql->execute();
        $result = $sql->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // READ — single supplier
    public function getSupplierById($supplierId)
    {
        $sql = $this->dbResult->prepare(
            "SELECT supplierid, supplierName, email, phone, address, d_status
             FROM suppliers_tbl
             WHERE supplierid = ?"
        );
        $sql->bind_param("s", $supplierId);
        $sql->execute();
        $result = $sql->get_result();

        return $result->fetch_assoc();
    }
}