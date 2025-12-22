<?php

/**
 * Role Constants
 * Define available roles in the application
 */
class Roles
{
    const ADMIN = 'admin';
    const CUSTOMER = 'customer';
    const STAFF = 'staff';
}

/**
 * Permission Constants
 * Define available permissions in the application
 */
class Permissions
{
    const VIEW_USERS = 'view_users';
    const CREATE_USER = 'create_user';
    const EDIT_USER = 'edit_user';
    const DELETE_USER = 'delete_user';
    
    const VIEW_CATEGORIES = 'view_categories';
    const CREATE_CATEGORY = 'create_category';
    const EDIT_CATEGORY = 'edit_category';
    const DELETE_CATEGORY = 'delete_category';
    
    const VIEW_MENU = 'view_menu';
    const CREATE_MENU_ITEM = 'create_menu_item';
    const EDIT_MENU_ITEM = 'edit_menu_item';
    const DELETE_MENU_ITEM = 'delete_menu_item';
    
    const CREATE_ORDER = 'create_order';
    const VIEW_OWN_ORDERS = 'view_own_orders';
    const VIEW_ALL_ORDERS = 'view_all_orders';
    const EDIT_ORDER = 'edit_order';
    const DELETE_ORDER = 'delete_order';
    
    const CREATE_RESERVATION = 'create_reservation';
    const VIEW_OWN_RESERVATIONS = 'view_own_reservations';
    const VIEW_ALL_RESERVATIONS = 'view_all_reservations';
}
?>
