<?php

class Db {
  public mysqli $conn;

  public function __construct(mysqli $conn) {
    $this->conn = $conn;
  }

  public function q(string $sql) {
    $res = mysqli_query($this->conn, $sql);
    if ($res === false) {
      throw new RuntimeException('DB query failed: ' . mysqli_error($this->conn));
    }
    return $res;
  }

  public function esc(?string $v): string {
    return mysqli_real_escape_string($this->conn, (string)$v);
  }

  public function one(string $sql): ?array {
    $res = $this->q($sql);
    $row = mysqli_fetch_assoc($res);
    return $row ?: null;
  }

  public function all(string $sql): array {
    $res = $this->q($sql);
    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
    return $rows;
  }
}

