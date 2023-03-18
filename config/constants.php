<?php

return [
    "permissions" => [
    "users",
    "roles",
    "products",
    "categories",
    "addresses",
    "orders",
    "shops",
    "product-categories",
    "cuisines",
    "slots",
    "delivery_charge",
    "delivery_boy_charge",
    "paid-orders",
//    "titles",
//    "sales",
//    "toppings"


    //Demand
    "services",
    "sub-services",
    "providers",
    "cars",
    "car-provider",
    "bookings",
    "weekdays"
],
    'cuisines' => ['indian', 'italy', 'mexican'],
    'types' => ['Order and Delivery', 'On Demand Service'],
    'categories' => ["Food","Grocery","Health and Fitness","Flowers and Gifts","Cosmetics","Pet supplies","Meat and Fish","Vegetable Supplies"],
    'demand_categories' => ["Car Rental","Electrical","Plumbing","Health and Fitness","Carpentry","Beauty Services","Fitness Coach","AC Services"],
    'user_profile_img' => 'admin-images/user/profile/',
    'shop_image' => 'admin-images/shop/shop-images/',
    'app_shop_banner_image' => 'admin-images/app-banners/',
    'app_banner_image' => 'admin-images/shop/app-banners/',
    'banner_image' => 'admin-images/shop/banners/',
    'product_image' => 'admin-images/product/product-images/',
    'category_icon_image' => 'admin-images/category/icon/',
    'category_banner_image' => 'admin-images/category/banners/',
    'notify' => 'admin-images/notify/',
    'demand_service_icon_image' => 'admin-images/demand/services/icon/',
    'demand_sub_service_icon_image' => 'admin-images/demand/sub-service/',
    'demand_shop_image' => 'admin-images/demand/shop/shop-images/',
    'demand_banner_image' => 'admin-images/demand/shop/banner-images/',
    'demand_car_image' => 'admin-images/demand/car/',
    'demand_ca_pro_image' => 'admin-images/demand/provider/car/',
    'app_banner_image' => 'admin-images/shop/app-banners/',
    'demand_ca_pro_about_image' => 'admin-images/demand/provider/car/about/',
    'demand_address_proof_image' => 'admin-images/demand/car/add-proofs/',
    'demand_id_proof_image' => 'admin-images/demand/car/id-proofs/',
    'notification_image' => 'admin-images/notification/',
    'currency' => env('CURRENCY', '₹'),



    'admin_email' => 'support@vdeliverz.in',
    'register_success' => [
        'title' => 'VDeliverz - Vendor Partner Registration',
        'a_push' => 'A New vendor has been signed up - Shop Name : xxxxxxxxxx ; Vendor Address : xxxxxxxxxxx ;  Vendor Phone Number: xxxxxxxxxxxxx ; - Verify and Approve Now',
        'a_sms' => 'A New vendor has been signed up - Shop Name : xxxxxxxxxx ; Vendor Address : xxxxxxxxxxx ;  Vendor Phone Number: xxxxxxxxxxxxx ; - Verify and Approve this VendorShop in Admin Dashboard',
        'a_email' => 'VDeliverz New Vendor Sign Up.  Registered Vendor : {#vendor_name#}; Email ID: {#vendor_email#}; Registerd Mobile Number: {#vendor_mobile#}; Shop Name: {#shop_name#}; Shop Address: {#street#},  {#area#}, {#city#}; Date Registered: {#date#}; Time Registered : {#time#};',
        'v_push' => 'Welcome onboard VDeliverz. Thanks for joining us, Our team will reach you soon.',
        'v_sms' => 'Thanks for Partnering with VDeliverz Delivery Platform. Our marketing team will get back to you shortly. In case of any delay kindly call us through mobile :  +91 75988 97020 || mail : hello@VDeliverzdelivery.com',
        'v_email' => 'VDeliverz Vendor Registration - Successful  Welcome to be  part of VDeliverz team. We are happy to see you here. You will receive your activation mail from VDeliverz Admin within 24hrs. Kindly be Patience. In case of missed communication kindly contact our Admin Support @ +91 75988 97020 or Mail us to hello@VDeliverzdelivery.com'
    ],
    'shop_approved' => [
        'title' => 'VDeliverz - Vendor Shop  Activated Successfully',
        'a_push' => 'The Shop  {#shop_name#}  is Verified and Activated by {#admin#}',
        'a_sms' => 'The Shop  {#shop_name#} registered in VDeliverz Delivery Platform is verified, activated and ready for Sale. Activated By Admin User Name: {#admin#};',
        'a_email' => 'VDeliverz Vendor Shop Activation Confirmation. Vendor Particulars ;  VDeliverz {#vendor_email#};  {#shop_name#}}; Shop Address; Vendor Contact Mobile Number; VDeliverz Comission Rate: xxxxx; No. Of Approved Outlets; Date of Registration; Date of Approval; Expiry Date; Activated By Admin User Name;  Admin User ID;',
        'v_push' => 'Hurray!  {#shop_name#}, You became an Verified Partner, Start adding your Products and increase your sales online through VDeliverz Delivery Platform',
        'v_sms' => 'The Shop  {#shop_name#} registered in VDeliverz Delivery Platform is verified, activated and ready for Sale. For more details check your mail or contact VDeliverz Admin.',
        'v_email' => 'VDeliverz Vendor Shop Activated Successfully. We are happy to announce that your Shop has been verified and Activated by VDeliverz Admin. Get ready and start adding your shop information, outlets Products and other details to display your shop in VDeliverz Customer App to enhance your sales and marketing. VDeliverz Vendor Login Link; VDeliverz Username and Password;  Shop Name; Shop Address; Vendor Contact Mobile Number; VDeliverz Comission Rate: xxxxx; No. Of Approved Outlets; Date of Registration; Date of Approval; Expiry Date; For any queries or support kindly reach our admin support centre @ +91 75988 97020 or Mail us to hello@VDeliverzdelivery.com',
    ],
    'outlet_added' => [
        'title' => 'VDeliverz Delivery - {#shop_name#} - added - a new outlet - {#outlet_name#}',
        'a_push' => 'VDeliverz - {#shop_name#}- #VendorNAme# - Requested or Registerd for New Outlet; Verify and Approve Now.',
        'a_sms' => 'VDeliverz -New Outlet Requested - {#shop_name#} - #ShopNumber#',
        'a_email' => 'VDeliverz - Vendor - Outlet Request Submission. Dear Admin, I {#shop_name#}, would like to add a new Outlet to my existing shop. I need to discuss about this and activate the same. Kindly contact me and complete the process at the earliest. Vendor Details : Shop Name : {#shop_name#}; Contact Number: {#vendor_mobile#}; Contact E-Mail ID: {#vendor_email#}; Date of Request: {#date#}; No. Of Outlets Requested: 1.',
        'v_push' => null,
        'v_sms' => 'VDeliverz Delivery - Greetings {#shop_name#}! - Thanks for showing your interest in adding A NEW OUTLET, our team will reach you soon.',
        'v_email' => 'VDeliverz Vendor- Outlet Requistion Submission - Successful  Welcome {#shop_name#}. We have received a request to add new outlet to your existing shop. Our team will reach your at the earliest to process your reques and soon you will receive your outlet activation mail from VDeliverz Admin. Kindly be Patience. In case of missed communication kindly contact our Admin Support @ +91 75988 97020 or Mail us to hello@VDeliverzdelivery.com',
    ],
    'coupon_added' => [
        'title' => 'VDeliverz Delivery Coupons',
        'c_push' => 'Wow ! You got a Coupon - {#coupon_code#}. Use and Shop more in your VDeliverz Delivery App and get your orders delviered at door steps',
        'a_push' => '',
        'a_sms' => '',
        'a_email' => '',
        'v_push' => '',
        'v_sms' => '',
        'v_email' => '',
    ],
    'payment' => [
        'title' => 'VDeliverz Delivery Payment',
        'c_push' => '"VDeliverz Payment, Your Payment is successful for this {#order_number#}. Your order will be processed soon. Thanks for shopping with VDeliverz. Your Payment has been not completed or declined, Try again or rech us for support. Thank you',
        'a_push' => '"VDeliverz Payment. You Received a Payment Request from the {#vendor_name#}',
        'a_sms' => '',
        'a_email' => '',
        'v_push' => 'VDeliverz Payment {#payment_state#}',
        'v_sms' => '',
        'v_email' => '',
    ],
    'order_placed' => [
        'title' => 'VDeliverz - Order Placed - {#order_number#}',
        'c_push' => 'VDeliverz Delivery, Hurray ! You placed an order successfully. Track your Order status',
        'a_push' => '{#shop_name#} - Order {#customer_name#} Placed an Order',
        'a_sms' => '',
        'a_email' => '',
        'v_push' => 'New order {#order_number#}, waiting to be processed.',
        'v_sms' => '"VDeliverz Delivery, You received an order {#order_number#}. Kindly Process and update the status',
        'v_email' => '',
    ],
    'order_status' => [
        'title' => 'VDeliverz Delivery - Order Traking',
        'c_push' => 'VDeliverz Order Status, Hey {#customer_name#}, {#shop_name#} {#order_state#} Your Order {#order_number#}. For More Details Track Here',
        'a_push' => '{#shop_name#} - Order {#customer_name#} Placed an Order',
        'a_sms' => '',
        'a_email' => '',
        'v_push' => 'VDeliverz Order Status, Hey {#shop_name#}, You {#order_state#} this Order {#order_number#}.',
        'v_sms'  => 'VDeliverz Order Status, Hey {#shop_name#}, You {#order_state#} this Order {#order_number#}.',
        'v_email' => '',
    ],
    'order_cancel' => [
        'title' => 'VDeliverz Order Status',
        'c_push' => 'VDeliverz Order Cancellation, Hey {#customer_name#},  You cancelled this Order {#order_number#}, Sorry to see you going.',
        'a_push' => 'VDeliverz Order Cancelled {#order_number#} - Cancelled by a Customer.',
        'a_sms' => '',
        'a_email' => '',
        'v_push' => 'VDeliverz {#order_number#}, This Order is cancelled by Customer',
        'v_sms' => 'VDeliverz Order Status {#order_number#},  {#customer_name#}, cancelled order',
        'v_email' => '',
    ],
    'order_picked' => [
        'title' => 'VDeliverz Order Picked Up',
        'c_push' => 'VDeliverz - Delivery, Your order is picked by {#delivery_boy#} from the vendor, you will receive it shortly',
        'a_push' => '"VDeliverz Delivery, {#order_number#} picked from {#shop_name#} by {#delivery_boy#} at {#date_time#}',
        'a_sms' => '',
        'a_email' => '',
        'v_push' => 'VDeliverz Delivery Boy - {#delivery_boy#} picked {#order_number#} at {#date_time#}',
        'v_sms' => '',
        'v_email' => '',
    ],
    'order_delivered' => [
        'title' => 'VDeliverz Order Delivered',
        'c_push' => 'VDeliverz Delivery, Hurray ! You received your order, Thanks for shopping with VDeliverz. Shop more and avail more coupons.',
        'a_push' => 'VDeliverz - Delivery, {#delivery_boy#} delivered {#order_number#} to {#customer_name#} at {#date_time#}',
        'a_sms' => '',
        'a_email' => '',
        'v_push' => 'VDeliverz - Delivery, {#order_number#}, is delivered to the {#customer_name#} by {#delivery_boy#} at {#date_time#}',
        'v_sms' => '',
        'v_email' => '',
    ],

];