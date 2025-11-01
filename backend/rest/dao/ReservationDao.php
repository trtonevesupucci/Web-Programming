<?php
require_once 'BaseDao.php';

/**
 * Reservation DAO
 * Handles database operations for reservations table
 */
class ReservationDao extends BaseDao
{
    protected $table_name;

    public function __construct()
    {
        $this->table_name = "reservations";
        parent::__construct($this->table_name);
    }

    /**
     * Get reservations by user ID
     * @param int $user_id
     * @return array
     */
    public function getReservationsByUser($user_id)
    {
        $stmt = $this->connection->prepare("SELECT * FROM reservations WHERE user_id = :user_id ORDER BY reservation_date DESC, reservation_time DESC");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get reservations by date
     * @param string $date
     * @return array
     */
    public function getReservationsByDate($date)
    {
        $stmt = $this->connection->prepare("SELECT * FROM reservations WHERE reservation_date = :date ORDER BY reservation_time");
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get reservations by status
     * @param string $status
     * @return array
     */
    public function getReservationsByStatus($status)
    {
        $stmt = $this->connection->prepare("SELECT * FROM reservations WHERE status = :status ORDER BY reservation_date, reservation_time");
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get upcoming reservations
     * @return array
     */
    public function getUpcomingReservations()
    {
        $stmt = $this->connection->prepare("SELECT * FROM reservations WHERE reservation_date >= CURDATE() AND status = 'confirmed' ORDER BY reservation_date, reservation_time");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get reservation with user details
     * @param int $reservation_id
     * @return array|false
     */
    public function getReservationWithUserDetails($reservation_id)
    {
        $query = "SELECT r.*, u.name as user_name, u.email as user_email, u.phone as user_phone
                  FROM reservations r
                  JOIN users u ON r.user_id = u.id
                  WHERE r.id = :reservation_id";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':reservation_id', $reservation_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Update reservation status
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status)
    {
        $stmt = $this->connection->prepare("UPDATE reservations SET status = :status WHERE id = :id");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Check availability for date and time
     * @param string $date
     * @param string $time
     * @return int - count of reservations at that time
     */
    public function checkAvailability($date, $time)
    {
        $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM reservations WHERE reservation_date = :date AND reservation_time = :time AND status != 'cancelled'");
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    /**
     * Get reservations count for today
     * @return int
     */
    public function getTodayReservationsCount()
    {
        $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM reservations WHERE reservation_date = CURDATE() AND status != 'cancelled'");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }
}
?>