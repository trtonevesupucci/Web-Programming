<?php
require_once __DIR__ . '/../dao/ReservationDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

/**
 * Reservation Service
 * Business logic for reservation operations
 */
class ReservationService
{
    private $reservationDao;
    private $userDao;

    public function __construct()
    {
        $this->reservationDao = new ReservationDao();
        $this->userDao = new UserDao();
    }

    /**
     * Get all reservations
     * @return array
     */
    public function getAll()
    {
        return $this->reservationDao->getAll();
    }

    /**
     * Get reservation by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid reservation ID");
        }
        return $this->reservationDao->getById($id);
    }

    /**
     * Get reservations by user ID
     * @param int $user_id
     * @return array
     */
    public function getReservationsByUser($user_id)
    {
        if (!is_numeric($user_id) || $user_id <= 0) {
            throw new Exception("Invalid user ID");
        }
        return $this->reservationDao->getReservationsByUser($user_id);
    }

    /**
     * Get reservations by date
     * @param string $date
     * @return array
     */
    public function getReservationsByDate($date)
    {
        if (empty($date)) {
            throw new Exception("Date is required");
        }
        return $this->reservationDao->getReservationsByDate($date);
    }

    /**
     * Get reservations by status
     * @param string $status
     * @return array
     */
    public function getReservationsByStatus($status)
    {
        $validStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception("Invalid status. Must be: " . implode(', ', $validStatuses));
        }
        return $this->reservationDao->getReservationsByStatus($status);
    }

    /**
     * Get upcoming reservations
     * @return array
     */
    public function getUpcomingReservations()
    {
        return $this->reservationDao->getUpcomingReservations();
    }

    /**
     * Get reservation with user details
     * @param int $reservation_id
     * @return array|false
     */
    public function getReservationWithUserDetails($reservation_id)
    {
        if (!is_numeric($reservation_id) || $reservation_id <= 0) {
            throw new Exception("Invalid reservation ID");
        }
        return $this->reservationDao->getReservationWithUserDetails($reservation_id);
    }

    /**
     * Add new reservation
     * @param array $data
     * @return array
     */
    public function add($data)
    {
        // Validation
        if (empty($data['user_id']) || !is_numeric($data['user_id'])) {
            throw new Exception("Valid user ID is required");
        }
        if (empty($data['reservation_date'])) {
            throw new Exception("Reservation date is required");
        }
        if (empty($data['reservation_time'])) {
            throw new Exception("Reservation time is required");
        }
        if (empty($data['guests']) || !is_numeric($data['guests']) || $data['guests'] <= 0) {
            throw new Exception("Valid number of guests is required");
        }
        if ($data['guests'] > 20) {
            throw new Exception("Maximum 20 guests allowed. Please contact us for larger parties.");
        }

        // Check if user exists
        $user = $this->userDao->getById($data['user_id']);
        if (!$user) {
            throw new Exception("User not found");
        }

        // Validate date is not in the past
        $reservationDateTime = strtotime($data['reservation_date'] . ' ' . $data['reservation_time']);
        if ($reservationDateTime < time()) {
            throw new Exception("Reservation date and time cannot be in the past");
        }

        // Check availability (max 10 reservations per time slot)
        $existingCount = $this->reservationDao->checkAvailability($data['reservation_date'], $data['reservation_time']);
        if ($existingCount >= 10) {
            throw new Exception("No tables available for this time slot. Please choose another time.");
        }

        // Set default status if not provided
        if (empty($data['status'])) {
            $data['status'] = 'pending';
        }

        return $this->reservationDao->add($data);
    }

    /**
     * Update reservation
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid reservation ID");
        }

        // Check if reservation exists
        $existingReservation = $this->reservationDao->getById($id);
        if (!$existingReservation) {
            throw new Exception("Reservation not found");
        }

        // Validate guests if provided
        if (isset($data['guests']) && (!is_numeric($data['guests']) || $data['guests'] <= 0)) {
            throw new Exception("Number of guests must be positive");
        }
        if (isset($data['guests']) && $data['guests'] > 20) {
            throw new Exception("Maximum 20 guests allowed");
        }

        // Validate status if provided
        if (isset($data['status'])) {
            $validStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
            if (!in_array($data['status'], $validStatuses)) {
                throw new Exception("Invalid status");
            }
        }

        // Validate date/time if provided
        if (isset($data['reservation_date']) && isset($data['reservation_time'])) {
            $reservationDateTime = strtotime($data['reservation_date'] . ' ' . $data['reservation_time']);
            if ($reservationDateTime < time()) {
                throw new Exception("Reservation date and time cannot be in the past");
            }
        }

        return $this->reservationDao->update($data, $id);
    }

    /**
     * Update reservation status
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid reservation ID");
        }

        $validStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception("Invalid status. Must be: " . implode(', ', $validStatuses));
        }

        // Check if reservation exists
        $reservation = $this->reservationDao->getById($id);
        if (!$reservation) {
            throw new Exception("Reservation not found");
        }

        return $this->reservationDao->updateStatus($id, $status);
    }

    /**
     * Delete reservation
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid reservation ID");
        }

        // Check if reservation exists
        $reservation = $this->reservationDao->getById($id);
        if (!$reservation) {
            throw new Exception("Reservation not found");
        }

        return $this->reservationDao->delete($id);
    }

    /**
     * Check availability for a time slot
     * @param string $date
     * @param string $time
     * @return array
     */
    public function checkAvailability($date, $time)
    {
        if (empty($date) || empty($time)) {
            throw new Exception("Date and time are required");
        }

        $count = $this->reservationDao->checkAvailability($date, $time);
        $available = ($count < 10);

        return [
            'date' => $date,
            'time' => $time,
            'available' => $available,
            'reservations_count' => $count,
            'slots_remaining' => max(0, 10 - $count)
        ];
    }

    /**
     * Get today's reservations count
     * @return int
     */
    public function getTodayReservationsCount()
    {
        return $this->reservationDao->getTodayReservationsCount();
    }
}
?>