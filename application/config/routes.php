<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/
$route['admin/login/logoff'] = "admin/login/logoff";
$route['admin/login'] = "admin/login";

$route['admin/slider'] = "admin/main/slider";

$route['admin/ajax/import/(.*)'] = "admin/ajax/import/$1";
$route['admin/ajax/edit_order/(.*)'] = "admin/ajax/edit_order/$1";
$route['admin/ajax/get_product_sizes/(.*)'] = "admin/ajax/get_product_sizes/$1";
$route['admin/ajax/get_order_products/(.*)'] = "admin/ajax/get_order_products/$1";
$route['admin/ajax/autocomplete/(.*)'] = "admin/ajax/autocomplete/$1";
$route['admin/ajax/mailer/(.*)/(.*)'] = "admin/ajax/mailer/$1/$2";
$route['admin/ajax/specifications/(.*)'] = "admin/ajax/specifications/$1";
$route['admin/ajax/getAdminMessage'] = "admin/ajax/getAdminMessage";
$route['admin/ajax/admin_action'] = "admin/ajax/admin_action";
$route['admin/ajax/users_search'] = "admin/ajax/users_search";
$route['admin/ajax/users'] = "admin/ajax/users";
$route['admin/ajax/send_foto'] = "admin/ajax/send_foto";
$route['admin/ajax/mail_send/(.*)'] = "admin/ajax/mail_send/$1";
$route['admin/ajax'] = "admin/ajax/index";
/////////////////
$route['admin/mailer/sms/add'] = "admin/mailer/sms_add";
$route['admin/mailer/sms/edit/(.*)'] = "admin/mailer/sms_edit/$1";
$route['admin/mailer/sms/send/(.*)'] = "admin/mailer/sms_send/$1";
$route['admin/mailer/sms/del/(.*)'] = "admin/mailer/sms_del/$1";
$route['admin/mailer/sms'] = "admin/mailer/sms";
$route['admin/mailer/send_new/(.*)'] = "admin/mailer/send_new/$1";
$route['admin/mailer/send/(.*)'] = "admin/mailer/send/$1";
$route['admin/mailer/show/(.*)'] = "admin/mailer/show/$1";
$route['admin/mailer/queue_message/(.*)'] = "admin/mailer/queueMessage/$1";
$route['admin/mailer/edit/(.*)'] = "admin/mailer/edit/$1";
$route['admin/mailer/del/(.*)'] = "admin/mailer/del/$1";
$route['admin/mailer/reset/(.*)'] = "admin/mailer/reset/$1";
$route['admin/mailer/add'] = "admin/mailer/add";
$route['admin/mailer/:num'] = "admin/mailer/index";
$route['admin/mailer'] = "admin/mailer/index";

$route['admin/import/add_new'] = "admin/add_new";
$route['admin/import/set_color'] = "admin/import/set_color";
$route['admin/import'] = "admin/import";

/////////////////

//// Настройки слайдера в админке
//$route['admin/slider/add']				= "admin/slider/add";
//$route['admin/slider/edit/(.*)']				= "admin/slider/edit/$1";
//$route['admin/slider/del']				= "admin/slider/del";
//$route['admin/slider/up']				= "admin/slider/up";
//$route['admin/slider/down']				= "admin/slider/down";
//$route['admin/slider/active']			= "admin/slider/active";
//$route['admin/slider']					= "admin/slider";

// Купоны
$route['admin/coupons/add'] = "admin/coupons/add";
$route['admin/coupons/edit/(.*)'] = "admin/coupons/edit/$1";
$route['admin/coupons/del/(.*)'] = "admin/coupons/del/$1";
$route['admin/coupons/active/(.*)'] = "admin/coupons/active/$1";
$route['admin/coupons/:num'] = "admin/coupons";
$route['admin/coupons'] = "admin/coupons";

$route['admin/users/type_active/(.*)'] = "admin/users/type_active/$1";
$route['admin/users/type_del/(.*)'] = "admin/users/type_del/$1";
$route['admin/users/types'] = "admin/users/users_types";
$route['admin/users/add'] = "admin/users/add";
$route['admin/users/edit/(.*)'] = "admin/users/edit/$1";
$route['admin/users/del/(.*)'] = "admin/users/del/$1";
$route['admin/users/active/(.*)'] = "admin/users/active/$1";
$route['admin/users/sendmail/(.*)'] = "admin/users/sendmail/$1";
$route['admin/users/export'] = "admin/users/export";
$route['admin/users/:num'] = "admin/users";
$route['admin/users'] = "admin/users";

$route['admin/categories'] = "admin/categories";
$route['admin/categories/add'] = "admin/categories/add";
$route['admin/categories/up/(.*)'] = "admin/categories/up/$1";
$route['admin/categories/down/(.*)'] = "admin/categories/down/$1";
$route['admin/categories/del/(.*)'] = "admin/categories/del/$1";
$route['admin/categories/edit/(.*)'] = "admin/categories/edit/$1";
$route['admin/categories/active/(.*)'] = "admin/categories/active/$1";

$route['admin/filter'] = "admin/filter";
$route['admin/filter/add'] = "admin/filter/add";
$route['admin/filter/up/(.*)'] = "admin/filter/up/$1";
$route['admin/filter/down/(.*)'] = "admin/filter/down/$1";
$route['admin/filter/del/(.*)'] = "admin/filter/del/$1";
$route['admin/filter/edit/(.*)'] = "admin/filter/edit/$1";
$route['admin/filter/active/(.*)'] = "admin/filter/active/$1";

$route['admin/pages'] = "admin/pages";
$route['admin/pages/add'] = "admin/pages/add";
$route['admin/pages/up/(.*)'] = "admin/pages/up/$1";
$route['admin/pages/down/(.*)'] = "admin/pages/down/$1";
$route['admin/pages/del/(.*)'] = "admin/pages/del/$1";
$route['admin/pages/edit/(.*)'] = "admin/pages/edit/$1";
$route['admin/pages/active/(.*)'] = "admin/pages/active/$1";

$route['admin/menus'] = "admin/menus";
$route['admin/menus/add'] = "admin/menus/add";
$route['admin/menus/up/(.*)'] = "admin/menus/up/$1";
$route['admin/menus/down/(.*)'] = "admin/menus/down/$1";
$route['admin/menus/del/(.*)'] = "admin/menus/del/$1";
$route['admin/menus/del-all'] = "admin/menus/del_all_post";
$route['admin/menus/edit/(.*)'] = "admin/menus/edit/$1";
$route['admin/menus/active/(.*)'] = "admin/menus/active/$1";

$route['admin/banners'] = "admin/banners/index";
$route['admin/banners/add'] = "admin/banners/add";
$route['admin/banners/del/(.*)'] = "admin/banners/del/$1";
$route['admin/banners/edit/(.*)'] = "admin/banners/edit/$1";

$route['admin/orders/create_torgsoft_file/(.*)'] = "admin/orders/create_torgsoft_file/$1";
$route['admin/orders/edit/(.*)'] = "admin/orders/edit/$1";
$route['admin/orders/edit2/(.*)'] = "admin/orders/edit2/$1";
$route['admin/orders/popup/(.*)'] = "admin/orders/popup/$1";
$route['admin/orders/orders_edit_block/(.*)'] = "admin/orders/orders_edit_block/$1";
$route['admin/orders/del/(.*)'] = "admin/orders/del/$1";
$route['admin/orders/(.*)'] = "admin/orders";
$route['admin/orders'] = "admin/orders";


$route['admin/shop/specifications/check_by_old/(.*)'] = "admin/shop/check_by_old/$1";
$route['admin/shop/specifications/recreate/(.*)'] = "admin/shop/specifications_recreate/$1";
$route['admin/shop/specifications/del/(.*)'] = "admin/shop/specifications_del/$1";
$route['admin/shop/specifications/(.*)'] = "admin/shop/specifications/$1";
$route['admin/shop/specifications'] = "admin/shop/specifications";

$route['admin/shop/create_extended_price'] = "admin/shop/createExtendedPrice";
$route['admin/shop/createCheckedPrice'] = "admin/shop/createCheckedPrice";
$route['admin/shop/category/(.*)'] = "admin/shop/category/$1";
$route['admin/shop/add'] = "admin/shop/add";
$route['admin/shop/add_image'] = "admin/shop/add_image";
$route['admin/shop/edit_image'] = "admin/shop/edit_image";
$route['admin/shop/edit/(.*)'] = "admin/shop/edit/$1";
$route['admin/shop/active/(.*)'] = "admin/shop/active/$1";
$route['admin/shop/always_first/(.*)'] = "admin/shop/always_first/$1";
$route['admin/shop/del/(.*)'] = "admin/shop/del/$1";
$route['admin/shop/break/(.*)'] = "admin/shop/break_torgsoft/$1";
$route['admin/shop/up/(.*)'] = "admin/shop/up/$1";
$route['admin/shop/down/(.*)'] = "admin/shop/down/$1";
$route['admin/shop/import'] = "admin/shop/import";
$route['admin/shop/export'] = "admin/shop/export";
$route['admin/shop/exportadwords'] = "admin/shop/exportAdwords";
$route['admin/shop/set_category'] = "admin/shop/set_category";
$route['admin/shop/currencies'] = "admin/shop/currencies";
$route['admin/shop/(.*)'] = "admin/shop/index/$1";
$route['admin/shop'] = "admin/shop/index";

$route['admin/articles/category/(.*)'] = "admin/articles/category/$1";
$route['admin/articles/add'] = "admin/articles/add";
$route['admin/articles/add_image'] = "admin/articles/add_image";
$route['admin/articles/edit_image'] = "admin/articles/edit_image";
$route['admin/articles/edit/(.*)'] = "admin/articles/edit/$1";
$route['admin/articles/active/(.*)'] = "admin/articles/active/$1";
$route['admin/articles/always_first/(.*)'] = "admin/articles/always_first/$1";
$route['admin/articles/del/(.*)'] = "admin/articles/del/$1";
$route['admin/articles/up/(.*)'] = "admin/articles/up/$1";
$route['admin/articles/down/(.*)'] = "admin/articles/down/$1";
$route['admin/articles/set_category'] = "admin/articles/set_category";
$route['admin/articles/(.*)'] = "admin/articles/index/$1";
$route['admin/articles'] = "admin/articles/index";

$route['admin/afisha'] = "admin/afisha/index";
$route['admin/afisha/add_image'] = "admin/afisha/add_image";
$route['admin/afisha/edit_image'] = "admin/afisha/edit_image";
$route['admin/afisha/add'] = "admin/afisha/add";
$route['admin/afisha/del/(.*)'] = "admin/afisha/del/$1";
$route['admin/afisha/active/(.*)'] = "admin/afisha/active/$1";
$route['admin/afisha/edit/(.*)'] = "admin/afisha/edit/$1";


$route['admin/schedule'] = "admin/schedule/index";
$route['admin/schedule/add'] = "admin/schedule/add";
$route['admin/schedule/edit/(.*)'] = "admin/schedule/edit/$1";
$route['admin/schedule/active/(.*)'] = "admin/schedule/active/$1";
$route['admin/schedule/del/(.*)'] = "admin/schedule/del/$1";

$route['admin/gallery/categories/add'] = "admin/gallery/categories_add";
$route['admin/gallery/categories/edit/(.*)'] = "admin/gallery/categoriesEdit/$1";
$route['admin/gallery/categories/up/(.*)'] = "admin/gallery/categoriesUp/$1";
$route['admin/gallery/categories/down/(.*)'] = "admin/gallery/categoriesDown/$1";
$route['admin/gallery/categories/del/(.*)'] = "admin/gallery/categoriesDel/$1";
$route['admin/gallery/categories/active/(.*)'] = "admin/gallery/categoriesActive/$1";
$route['admin/gallery/options/edit'] = "admin/gallery/optionsEdit";
$route['admin/gallery/options'] = "admin/gallery/options";
$route['admin/gallery/categories'] = "admin/gallery/categories";


$route['admin/gallery/zip_import'] = "admin/gallery/zipImport";
$route['admin/gallery/add'] = "admin/gallery/addFoto";
$route['admin/gallery/edit/(.*)'] = "admin/gallery/editFoto/$1";
$route['admin/gallery/up/(.*)'] = "admin/gallery/upFoto/$1";
$route['admin/gallery/down/(.*)'] = "admin/gallery/downFoto/$1";
$route['admin/gallery/del/(.*)'] = "admin/gallery/delFoto/$1";
$route['admin/gallery/active/(.*)'] = "admin/gallery/activeFoto/$1";
$route['admin/gallery/set_category'] = "admin/gallery/set_category";
$route['admin/gallery/:num'] = "admin/gallery/index";
$route['admin/gallery'] = "admin/gallery/index";

$route['admin/images'] = "admin/images/index";

$route['admin/comments/del/(.*)'] = "admin/comments/del/$1";
$route['admin/comments/active/(.*)'] = "admin/comments/active/$1";
$route['admin/comments/(.*)'] = "admin/comments/index";
$route['admin/comments'] = "admin/comments/index";


$route['admin/options/add'] = "admin/options/add";
$route['admin/options/edit/(.*)'] = "admin/options/edit/$1";
$route['admin/options/set_module/(.*)'] = "admin/options/set_module/$1";
$route['admin/options/set_module'] = "admin/options/set_module";
$route['admin/options/del/(.*)'] = "admin/options/del/$1";
$route['admin/options'] = "admin/options/index";
//$route['(.*)/:num']

$route['admin'] = "admin/main";
$route['admin/main/edit'] = "admin/main/edit";
$route['admin/banners/active/(.*)'] = "admin/banners/active/$1";

$route['admin/blogs/options/del/(.*)'] = "admin/blogs/blogOptionsDel/$1";
$route['admin/blogs/options/edit/(.*)'] = "admin/blogs/blogOptionsEdit/$1";
$route['admin/blogs/options/add'] = "admin/blogs/blogOptionsAdd";
$route['admin/blogs/options'] = "admin/blogs/blogOptions";
$route['admin/blogs/invitation_code_add'] = "admin/blogs/invitation_code_add";
$route['admin/blogs/invitation_code_del/(.*)'] = "admin/blogs/invitation_code_del/$1";
$route['admin/blogs/invitation_codes'] = "admin/blogs/invitation_codes";
$route['admin/blogs/blog_content_edit/(.*)'] = "admin/blogs/blog_content_edit/$1";
$route['admin/blogs/blog/(.*)/:num'] = "admin/blogs/blog/$1"; // ïàãèíàöèÿ
$route['admin/blogs/blog/(.*)'] = "admin/blogs/blog/$1";
$route['admin/blogs/edit/(.*)'] = "admin/blogs/edit/$1";
$route['admin/blogs/del/(.*)'] = "admin/blogs/del/$1";
$route['admin/blogs/:num'] = "admin/blogs/index"; // ïàãèíàöèÿ
$route['admin/blogs'] = "admin/blogs/index";
// äîáàâèòü ðîóòû íà active è del !!!!!!!!!!

// ÔÎÐÓÌ
$route['admin/forum/sections/add'] = "admin/forum/sections_add";
$route['admin/forum/sections/edit/(.*)'] = "admin/forum/sections_edit/$1";
$route['admin/forum/sections/del/(.*)'] = "admin/forum/sections_del/$1";
$route['admin/forum/sections/active/(.*)'] = "admin/forum/sections_active/$1";
$route['admin/forum/sections/up/(.*)'] = "admin/forum/sections_up/$1";
$route['admin/forum/sections/down/(.*)'] = "admin/forum/sections_down/$1";
$route['admin/forum/sections'] = "admin/forum/sections";
$route['admin/forum/sections/:num'] = "admin/forum/sections"; // ïàãèíàöèÿ

$route['admin/forum/topics/add'] = "admin/forum/topics_add";
$route['admin/forum/topics/edit/(.*)'] = "admin/forum/topics_edit/$1";
$route['admin/forum/topics/del/(.*)'] = "admin/forum/topics_del/$1";
$route['admin/forum/topics/active/(.*)'] = "admin/forum/topics_active/$1";
//$route['admin/forum/topics/up/(.*)']                  = "admin/forum/topics_up/$1";
//$route['admin/forum/topics/down/(.*)']                = "admin/forum/topics_down/$1";
$route['admin/forum/topics/show_only_section_id/(.*)'] = "admin/forum/topics_show_only_section_id/$1";
$route['admin/forum/topics/show_only_section_id'] = "admin/forum/topics_show_only_section_id";
$route['admin/forum/topics/search'] = "admin/forum/topics_search";
$route['admin/forum/topics/:num'] = "admin/forum/topics"; // ïàãèíàöèÿ
$route['admin/forum/topics'] = "admin/forum/topics";

//$route['admin/forum/messages/add']              = "admin/forum/messages_add";
$route['admin/forum/messages/reserve_only'] = "admin/forum/reserve_only";
$route['admin/forum/messages/search'] = "admin/forum/messages_search";
$route['admin/forum/messages/messages_search_user'] = "admin/forum/messages_search_user";
$route['admin/forum/messages/edit/(.*)'] = "admin/forum/messages_edit/$1";
$route['admin/forum/messages/del/(.*)'] = "admin/forum/messages_del/$1";
$route['admin/forum/messages/active/(.*)'] = "admin/forum/messages_active/$1";
$route['admin/forum/messages/reserve/(.*)'] = "admin/forum/messages_reserve/$1";
$route['admin/forum/messages'] = "admin/forum/messages";
//$route['admin/forum/messages/:num']             = "admin/forum/messages"; // ïàãèíàöèÿ

$route['admin/forum/options/add'] = "admin/forum/options_add";
$route['admin/forum/options/edit/(.*)'] = "admin/forum/options_edit/$1";
$route['admin/forum/options/del/(.*)'] = "admin/forum/options_del/$1";
$route['admin/forum/options'] = "admin/forum/options";

$route['admin/subscription'] = "admin/subscription/index";

$route['admin/tkdz'] = "admin/main/tkdz";

$route['admin/forum'] = "admin/forum/sections";
// -- //

//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
$route['parser/goroskop'] = "parser/goroskop";
$route['parser/recipes'] = "parser/recipes";
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////


////   AJAX
$route['ajax/upload_review_foto/(.*)'] = "ajax/upload_review_foto/$1";
$route['ajax/show_block/(.*)'] = "ajax/showBlock/$1";
$route['ajax/fast_order'] = "ajax/fast_order";
$route['ajax/get_my_cart_details'] = "ajax/get_my_cart_details";
$route['ajax/get_addr/(.*)'] = "ajax/getAddr/$1";
$route['ajax/set_cookies'] = "ajax/set_cookies";
$route['ajax/fast_order_form'] = "ajax/fast_order_form";
$route['ajax/rate'] = "ajax/rate";
$route['ajax/add_review'] = "ajax/add_review";
$route['ajax/set_shop_opt_from'] = "ajax/set_shop_opt_from";
$route['ajax/reloadMyCartTable'] = "ajax/reloadMyCartTable";
$route['ajax/get_fast_order'] = "ajax/get_fast_order";
$route['ajax/say_me_available'] = "ajax/say_me_available";
$route['ajax/autocomplete'] = "ajax/autocomplete";
$route['ajax/adress'] = "ajax/adress";
$route['ajax/country'] = "ajax/country";
$route['ajax/setka/(.*)'] = "ajax/setka/$1";
$route['ajax/getnextreviews'] = "ajax/getNextReviews";
$route['ajax/getnextrows'] = "ajax/getNextRows";
$route['ajax/umnog/(.*)/(.*)'] = "ajax/umnog/$1/$2";
$route['ajax/cart_save'] = "ajax/cart_save";
$route['ajax/to_cart'] = "ajax/to_cart";
$route['ajax/cart_actions'] = "ajax/cart_actions";
$route['ajax/export/(.*)'] = "ajax/export/$1";
$route['ajax/to_market/(.*)'] = "ajax/to_market/$1";
$route['ajax/userdata/(.*)'] = "ajax/userdata/$1";
$route['ajax/create_one_click_order'] = "ajax/create_one_click_order";
$route['ajax/vk/(.*)'] = "ajax/vk/$1";


///// VK Market
$route['vk/auth'] = "vk/auth";

//$route['vk/create_album']     = "vk/createAlbum";
$route['vk/export_category/(.*)'] = "vk/exportCategory/$1";

$route['vk/exporttomarket/(.*)/(.*)'] = "vk/exportToMarket/$1/$2";
$route['vk/exporttomarket/(.*)'] = "vk/exportToMarket/$1";


$route['vk/exportcategories'] = "vk/exportCategories";

$route['vk/exporttomarket'] = "vk/exportToMarket";

///////////////
$route['export/to-albums'] = "export/toAlbums";
$route['export/to-market'] = "export/toMarket";
$route['export/email_required'] = "export/email_required";
$route['export/test'] = "export/test";


/////////////////////////////////////////////////////////

$route['ajax/admin_save_price'] = "ajax/admin_save_price";
//////////////////////////////////////////////////////////////////////////
/////// СПОСОБЫ ОПЛАТЫ

$route['payment/interkassa/(.*)'] = 'shop/interkassa/$1';
$route['payed/interkassa'] = 'shop/interkassa_payed';

$route['payment/walletone/(.*)'] = 'shop/walletone/$1';
$route['payed/walletone/(.*)'] = 'shop/walletone_payed/$1';

$route['payment/interkassa/(.*)'] = 'shop/interkassa/$1';
$route['payed/interkassa/(.*)'] = 'shop/interkassa/$1';

$route['payment/privat24/(.*)'] = 'shop/privat/$1';
$route['payed/privat24/(.*)'] = 'shop/privat_payed/$1';

$route['payment/to_cart/(.*)'] = 'shop/paymentToCart/$1';
$route['payed/to_cart/(.*)'] = 'shop/paymentToCart/$1';

$route['payment/other/(.*)'] = 'shop/paymentOther/$1';
$route['payed/other/(.*)'] = 'shop/paymentOther/$1';

$route['payment/liqpay/(.*)'] = 'shop/liqpay/$1';
$route['payment/liqpay'] = 'shop/liqpay';
$route['payed/liqpay/(.*)'] = 'shop/liqpay_payed/$1';
////////////////////////////////////////////////////////////

$route['mailer'] = "mailer/index";
$route['mailer_send/(.*)'] = "mailer/mailerSend/$1";

$route['add_to_cart'] = "shop/add_to_cart";
$route['show_one_click_form'] = "shop/getOneClickForm";
$route['my_cart/sended'] = "shop/sended";
$route['my_cart/sended/(.*)'] = "shop/sended/$1";
$route['my_cart/complete'] = "shop/complete_my_cart";
$route['my_cart'] = "shop/my_cart";
$route['order/error'] = "shop/orderError";
$route['order'] = "shop/order";

//$route['gallery/(.*)/image/(.*)']            = "gallery/image/$2/$1";
//$route['gallery/(.*)/(.*)/image/(.*)']            = "gallery/image/$3/$2/$1";
//
//$route['gallery/add']                   = "gallery/add";
//
//$route['gallery/(.*)/(.*)/:num']         = "gallery/category/$2/$1";
//$route['gallery/(.*)/:num']             = "gallery/category/$1";
//$route['gallery/(.*)/(.*)']             = "gallery/category/$2/$1";
//$route['gallery/:num']                  = "gallery";
//$route['gallery/(.*)']                  = "gallery/category/$1";
//$route['gallery']                       = "gallery";


$route['comments/add'] = "comments/add";
$route['comments/answer/(.*)'] = "comments/answer/$1";

$route['search/(.*)'] = "categories/search";
$route['search'] = "categories/search";
$route['archive'] = "categories/archive";

$route['banner/(.*)'] = "categories/banner/$1";

$route['rss/afisha'] = "rss/afisha";
$route['rss'] = "rss/index";
$route['ukrnetrss'] = "rss/ukrnetrss";

$route['subscription'] = "main/subscription";

$route['login/soc'] = "login/soc";
$route['login/logout'] = "login/logout";
$route['login'] = "login/index";

$route['register/activation/(.*)/(.*)'] = "register/activation/$1/$2";
$route['register/send-activation-code/(.*)'] = "register/send_activation_code/$1";
$route['register/forgot'] = "register/forgot";
$route['register/set_password/(.*)/(.*)'] = "register/set_password/$1/$2";
$route['register/(.*)'] = "register/index";
$route['register'] = "register/index";


// Äîáàâëåíèå
$route['add/article'] = "add/addArticle";
//

$route['user/set_mailer/(.*)/(.*)'] = "user/setMailer/$1/$2";
$route['user/mypage'] = "user/mypage";
$route['user/dropship_client_adress/(.*)'] = "user/dropship_client_adress/$1";
$route['user/dropship_client_adress'] = "user/dropship_client_adress";
$route['user/order-cancel/(.*)'] = "user/order_cancel/$1";
$route['user/order-done/(.*)'] = "user/order_done/$1";
$route['user/order-details/(.*)'] = "user/order_details/$1";
$route['user/edit-mypage'] = "user/edit_mypage";
$route['user/upload/foto'] = "register/fotoUpload";
$route['rating/(.*)'] = "user/rating/$1";
$route['users/(.*)'] = "user/showUserPage/$1";
$route['users'] = "user/users";

// FORUM
//$route['forum']                             = "forum/index";

// Áëîã
//
//$route['blog/add-blog-content/(.*)']        = "blog/add_blog_content/$1";
//$route['blog/edit-blog-content/(.*)']       = "blog/edit_blog_content/$1";
//$route['blog/del-blog-content/(.*)']        = "blog/del_blog_content/$1";
//
//$route['blog/user/(.*)/!:num']              = "blog/showBlog/$1";
//$route['blog/user/(.*)/(.*)']               = "blog/showContent/$1/$2";
//$route['blog/user/(.*)']                    = "blog/showBlog/$1";
//$route['blog/edit/(.*)']                    = "blog/edit_blog_content/$1";
//$route['blog/create/(.*)']                  = "blog/createBlog";
//$route['blog/create']                       = "blog/createBlog";
//$route['blog']                              = "blog/index";
//

$route['price_download'] = "main/price_download";

$route['order'] = "main/order";

$route['sitemap_xml'] = "sitemap/xml";

$route['sitemap/gallery/(.*)'] = "sitemap/gallery_category/$1";
$route['sitemap/gallery'] = "sitemap/gallery";
$route['sitemap/(.*)/!:num'] = "sitemap/category/$1";
$route['sitemap/(.*)'] = "sitemap/category/$1";
$route['sitemap'] = "sitemap/index";

$route['banner_redirect/(.*)'] = "main/banner_redirect/$1";


// CRONs //
$route['cron/price_download_count'] = "cron/price_download_count";
$route['cron/creat_new_orders_files'] = "cron/createNewOrdersFiles";
$route['cron/update_exchange'] = "cron/updateExchange";
$route['cron/mailer_send'] = "cron/mailerSend";
$route['cron/create_mailer_sale_crons'] = "cron/createMailerSaleCrons";
$route['cron/create_mailer_new_crons'] = "cron/createMailerNewCrons";
$route['cron/say_me_available'] = "cron/say_me_available";
$route['cron/create_yml'] = "cron/create_yml";
$route['cron/create_prom_yml'] = "cron/create_prom_yml";
$route['cron/sms_send'] = "cron/sms_send";
$route['cron/bd_mailing'] = "mailer/bd_mailing";
$route['cron/create_zip_price'] = "cron/create_zip_price";
//$route['cron/create_zip_file']         = "cron/create_zip_file";
$route['cron/prom_orders_import'] = "cron/prom_orders_import";
$route['cron/test'] = "cron/test";
$route['cron'] = "cron";

// /CRONs //


$route['action'] = "categories/action";
$route['action/!:num'] = "categories/action";


$route['(.*)/filter/(.*)/(.*)'] = "categories/category/$1/$2/$3";
$route['!:num'] = "main";
$route['(.*)/(.*)/!:num'] = "categories/subcategory/$2/$1";
$route['(.*)/(.*)/(.*)'] = "categories/subcategory/$3/$2";
$route['(.*)/!:num'] = "categories/category/$1";
$route['(.*)/(.*)'] = "categories/subcategory/$2/$1";
$route['(.*)'] = "categories/category/$1";


$route['default_controller'] = "main";
$route['404_override'] = '';

/* End of file routes.php */
/* Location: ./application/config/routes.php */