<?php

require_once 'BaseService.php';
require_once __DIR__ . '/../dao/ReservationDao.php';

class ReservationService extends BaseService {
    
    public function __construct() {
        $dao = new ReservationDao();
        parent::__construct($dao);
    }
    
    // Get all reservations for a specific user
    public function getByUserId($user_id) {
        return $this->dao->getByUserId($user_id);
    }
    
    // Get reservations by date
    public function getByDate($date) {
        return $this->dao->getByDate($date);
    }
    
    // Get reservations by status
    public function getByStatus($status) {
        return $this->dao->getByStatus($status);
    }
    
    // Business logic: Validate reservation data before creating
    public function createReservation($data) {
        // Validate required fields
        if (!isset($data['user_id']) || !isset($data['reservation_date']) || !isset($data['number_of_guests'])) {
            throw new Exception('User ID, reservation date, and number of guests are required.');
        }
        
        // Validate number of guests is positive
        if ($data['number_of_guests'] <= 0) {
            throw new Exception('Number of guests must be a positive value.');
        }
        
        // Validate reservation date is not in the past
        $reservation_date = strtotime($data['reservation_date']);
        $current_date = strtotime(date('Y-m-d'));
        
        if ($reservation_date < $current_date) {
            throw new Exception('Reservation date cannot be in the past.');
        }
        
        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }
        
        return $this->create($data);
    }
    
    // Business logic: Update reservation with validation
    public function updateReservation($id, $data) {
        // Validate number of guests if provided
        if (isset($data['number_of_guests']) && $data['number_of_guests'] <= 0) {
            throw new Exception('Number of guests must be a positive value.');
        }
        
        // Validate reservation date if provided
        if (isset($data['reservation_date'])) {
            $reservation_date = strtotime($data['reservation_date']);
            $current_date = strtotime(date('Y-m-d'));
            
            if ($reservation_date < $current_date) {
                throw new Exception('Reservation date cannot be in the past.');
            }
        }
        
        return $this->update($id, $data);
    }
    
    // Business logic: Cancel a reservation
    public function cancelReservation($id) {
        return $this->update($id, ['status' => 'cancelled']);
    }
    
    // Business logic: Confirm a reservation
    public function confirmReservation($id) {
        return $this->update($id, ['status' => 'confirmed']);
    }
}

?>