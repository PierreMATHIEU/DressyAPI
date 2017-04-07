<?php
require dirname(__FILE__) . '/DbConnect.php';
class DbClotheHandler {

  private $conn;

  function __construct() {
      require_once dirname(__FILE__) . '/DbConnect.php';
      // opening db connection
      $db = new DbConnect();
      $this->conn = $db->getDB();
  }

  /**
 * Create clothe
 * @param String $clotheName
 * @param String $clotheColor
 * @param String $clotheReference
 */
public function createClothe($clotheName, $clotheColor, $clotheReference) {
      $stmt = $this->conn->prepare("INSERT INTO clothe(clothe_brand_id, clothe_category_id, clothe_material_id, clothe_name, clothe_color, clothe_reference) VALUES (1, 1, 1, ?, ?, ?)");
      $stmt->bind_param("sss", $clotheName, $clotheColor, $clotheReference);

      $result = $stmt->execute();
      $stmt->close();

      if ($result) {
          // User successfully inserted
          return CLOTHE_CREATED_SUCCESSFULLY;
      } else {
          // Failed to create user
          return CLOTHE_CREATE_FAILED;
      }
    }

    /**
   * View clothing
   * @param int $clothingId
   */
  public function viewClothe($clothingId) {
        $sth = $this->conn->prepare("SELECT * FROM clothing WHERE clothing_id = :id");
        $sth->bindParam(':id', $clothingId, PDO::PARAM_INT);
        $sth->execute();
        $clothe = $sth->fetch(PDO::FETCH_OBJ);

        if ($sth) {
            // User successfully inserted
            return $clothe;
        } else {
            // Failed to create user
            return false;
        }
      }


}
