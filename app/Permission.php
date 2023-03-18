<?php

namespace App;

class Permission extends \Spatie\Permission\Models\Permission
{
    public static function demandPermissions(){
        return [
            "create_provider-cars",
            "edit_provider-cars",
            "view_provider-cars",
            "delete_provider-cars",
            "create_cars",
            "view_cars",
            "edit_cars",
            "delete_cars",
            "create_banners",
            "view_banners",
            "edit_banners",
            "delete_banners",
            "create_services",
            "edit_services",
            "view_services",
            "delete_services",
            "create_providers",
            "edit_providers",
            "view_providers",
            "delete_providers",
            "create_car-provider",
            "edit_car-provider",
            "view_car-provider",
            "delete_car-provider",
            "create_sub-services",
             "edit_sub-services" , "view_sub-services" ,
             "delete_sub-services" , "create_weekdays" ,
             "edit_weekdays" , "view_weekdays" , "delete_weekdays" ,
              "view_bookings" , "edit_bookings" , "create_bookings" , "delete_bookings","Order_review",
        ];
    }

    public static function defaultPermissions()
    {
        return [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',

            'view_addresses',
            'create_addresses',
            'edit_addresses',
            'delete_addresses',

            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',

            'view_product-categories',
            'create_product-categories',
            'edit_product-categories',
            'delete_product-categories',

            'view_products',
            'create_products',
            'edit_products',
            'delete_products',

            'view_shops',
            'create_shops',
            'edit_shops',
            'delete_shops',

            'view_coupons',
            'create_coupons',
            'edit_coupons',
            'delete_coupons',

            'view_types',
            'create_types',
            'edit_types',
            'delete_types',

            'view_orders',
            'create_orders',
            'edit_orders',
            'delete_orders',

            'view_cuisines',
            'create_cuisines',
            'edit_cuisines',
            'delete_cuisines',

            'view_slots',
            'create_slots',
            'edit_slots',
            'delete_slots',

            'view_delivery_charge',
            'create_delivery_charge',
            'edit_delivery_charge',
            'delete_delivery_charge',

            'view_delivery_boy_charge',
            'create_delivery_boy_charge',
            'edit_delivery_boy_charge',
            'delete_delivery_boy_charge',

            'create_paid-orders',
            'edit_paid-orders',
            'view_paid-orders',
            'delete_paid-orders',
            'Order_review'
        ];
    }
}