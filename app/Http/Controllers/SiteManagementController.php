<?php
/**
 * Class SiteManagementController
 *
 * @category Worketic
 *
 * @package Worketic
 * @author  Amentotech <theamentotech@gmail.com>
 * @license http://www.amentotech.com Amentotech
 * @link    http://www.amentotech.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Language;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMailable;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Redirect;
use Hash;
use Auth;
use DB;
use App\Helper;
use App\Profile;
use Session;
use Storage;
use App\Report;
use App\Job;
use App\Proposal;
use App\SiteManagement;
use App\Page;
use Illuminate\Support\Arr;

/**
 * Class SiteManagementController
 *
 */
class SiteManagementController extends Controller
{

    /**
     * Defining scope of variable
     *
     * @access public
     * @var    array $category
     */
    protected $settings;

    /**
     * Create a new controller instance.
     *
     * @param mixed $settings get sitemanagement model
     *
     * @return void
     */
    public function __construct(SiteManagement $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Show site settings form.
     *
     * @access public
     *
     * @return View
     */
    public function settings()
    {
        $client_id = '';
        $payment_password = '';
        $existing_payment_secret = '';
        $data = $this->settings::getMetaValue('email_data');
        $from_email = !empty($data[0]['from_email']) ? $data[0]['from_email'] : null;
        $from_email_id = !empty($data[0]['from_email_id']) ? $data[0]['from_email_id'] : null;
        $sender_name = !empty($data[0]['sender_name']) ? $data[0]['sender_name'] : null;
        $sender_tagline = !empty($data[0]['sender_tagline']) ? $data[0]['sender_tagline'] : null;
        $sender_url = !empty($data[0]['sender_url']) ? $data[0]['sender_url'] : null;
        $email_logo = !empty($data[0]['email_logo']) ? $data[0]['email_logo'] : null;
        $email_banner = !empty($data[0]['email_banner']) ? $data[0]['email_banner'] : null;
        $sender_avatar = !empty($data[0]['sender_avatar']) ? $data[0]['sender_avatar'] : null;
        $settings = $this->settings::getMetaValue('settings');
        $title = !empty($settings[0]['title']) ? $settings[0]['title'] : null;
        $email = !empty($settings[0]['email']) ? $settings[0]['email'] : null;
        $connects_per_job = !empty($settings[0]['connects_per_job']) ? $settings[0]['connects_per_job'] : null;
        $gmap_api_key = !empty($settings[0]['gmap_api_key']) ? $settings[0]['gmap_api_key'] : null;
        $logo = !empty($settings[0]['logo']) ? $settings[0]['logo'] : null;
        $payout_settings = $this->settings::getMetaValue('commision');
        $existing_currency = !empty($payout_settings[0]['currency']) ? $payout_settings[0]['currency'] : '';
        $commision = !empty($payout_settings[0]['commision']) ? $payout_settings[0]['commision'] : null;
        $payment_gateway = !empty($payout_settings[0]['payment_method']) ? $payout_settings[0]['payment_method'] : array();
        $min_payout = !empty($payout_settings[0]['min_payout']) ? $payout_settings[0]['min_payout'] : 0;
        $existing_payment_settings = $this->settings::getMetaValue('payment_settings');
        $client_id = !empty($existing_payment_settings[0]['client_id']) ? $existing_payment_settings[0]['client_id'] : '';
        $payment_password = !empty($existing_payment_settings[0]['paypal_password']) ? $existing_payment_settings[0]['paypal_password'] : '';
        $existing_payment_secret = !empty($existing_payment_settings[0]['paypal_secret']) ? $existing_payment_settings[0]['paypal_secret'] : '';
        $footer_settings = $this->settings::getMetaValue('footer_settings');
        $footer_logo = !empty($footer_settings['footer_logo']) ? $footer_settings['footer_logo'] : null;
        $footer_desc = !empty($footer_settings['description']) ? $footer_settings['description'] : null;
        $footer_copyright = !empty($footer_settings['copyright']) ? $footer_settings['copyright'] : 'Worketic. All Rights Reserved.';
        $menu_pages = !empty($footer_settings['pages']) ? $footer_settings['pages'] : array();
        $menu_pages_1 = !empty($footer_settings['menu_pages_1']) ? $footer_settings['menu_pages_1'] : array();
        $menu_title_1 = !empty($footer_settings['menu_title_1']) ? $footer_settings['menu_title_1'] : '';
        $menu_title_2 = !empty($footer_settings['menu_title_2']) ? $footer_settings['menu_title_2'] : '';
        $pages = Page::select('title', 'id')->get()->pluck('title', 'id');
        $social_list = Helper::getSocialData();
        $social_unserialize_array = SiteManagement::getMetaValue('socials');
        $unserialize_menu_array = SiteManagement::getMetaValue('search_menu');
        $menu_title = DB::table('site_managements')->select('meta_value')->where('meta_key', 'menu_title')->get()->first();
        $currency = array_pluck(Helper::currencyList(), 'code', 'code');
        $payment_methods = Helper::getPaymentMethodList();
        $stripe_settings = $this->settings::getMetaValue('stripe_settings');
        $stripe_key = !empty($stripe_settings) ? $stripe_settings[0]['stripe_key'] : '';
        $stripe_secret = !empty($stripe_settings) ? $stripe_settings[0]['stripe_secret'] : '';
        $languages = Helper::getTranslatedLang();
        $selected_language = !empty($settings[0]['language']) ? $settings[0]['language'] : '' ;
        $currency = array_pluck(Helper::currencyList(), 'code', 'code');
        $register_form = $this->settings::getMetaValue('reg_form_settings');
        $reg_one_title = !empty($register_form) && !empty($register_form[0]['step1-title']) ? $register_form[0]['step1-title'] : '';
        $reg_one_subtitle = !empty($register_form) && !empty($register_form[0]['step1-subtitle']) ? $register_form[0]['step1-subtitle'] : '';
        $reg_two_title = !empty($register_form) && !empty($register_form[0]['step2-title']) ? $register_form[0]['step2-title'] : '';
        $reg_two_subtitle = !empty($register_form) && !empty($register_form[0]['step2-subtitle']) ? $register_form[0]['step2-subtitle'] : '';
        $term_note = !empty($register_form) && !empty($register_form[0]['step2-term-note']) ? $register_form[0]['step2-term-note'] : '';
        $reg_three_title = !empty($register_form) && !empty($register_form[0]['step3-title']) ? $register_form[0]['step3-title'] : '';
        $reg_three_subtitle = !empty($register_form) && !empty($register_form[0]['step3-subtitle']) ? $register_form[0]['step3-subtitle'] : '';
        $register_image = !empty($register_form) && !empty($register_form[0]['register_image']) ? $register_form[0]['register_image'] : '';
        $reg_page = !empty($register_form) && !empty($register_form[0]['step3-page']) ? $register_form[0]['step3-page'] : '';
        $reg_four_title = !empty($register_form) && !empty($register_form[0]['step4-title']) ? $register_form[0]['step4-title'] : '';
        $reg_four_subtitle = !empty($register_form) && !empty($register_form[0]['step4-subtitle']) ? $register_form[0]['step4-subtitle'] : '';
        $icons = Helper::getIconList();
        $dash_icons  = SiteManagement::getMetaValue('icons');
        return view(
            'back-end.admin.settings.index',
            compact(
                'from_email', 'from_email_id', 'sender_name',
                'sender_tagline', 'sender_url', 'email_logo', 'email_banner',
                'sender_avatar', 'title', 'email', 'logo', 'commision',
                'existing_payment_settings', 'connects_per_job', 'footer_logo',
                'footer_desc', 'social_list', 'social_unserialize_array',
                'footer_copyright', 'pages', 'menu_pages', 'menu_pages_1',
                'unserialize_menu_array', 'menu_title_1', 'menu_title_2', 'menu_title',
                'client_id', 'payment_password', 'existing_payment_secret',
                'currency', 'existing_currency', 'gmap_api_key', 'min_payout',
                'payment_methods', 'payment_gateway', 'stripe_key',
                'stripe_secret', 'languages', 'selected_language', 'reg_one_title',
                'reg_one_subtitle', 'reg_two_title', 'reg_two_subtitle', 'reg_three_title',
                'reg_three_subtitle', 'register_image', 'reg_four_title', 'reg_four_subtitle',
                'reg_page', 'term_note', 'icons', 'dash_icons'
            )
        );
    }

    /**
     * Show home page settings form.
     *
     * @access public
     *
     * @return View
     */
    public function homePageSettings()
    {
        $home_settings = $this->settings::getMetaValue('home_settings');
        $section_settings = $this->settings::getMetaValue('section_settings');
        $banner_bg = !empty($home_settings[0]['home_banner']) ? $home_settings[0]['home_banner'] : null;
        $banner_bg_image = !empty($home_settings[0]['home_banner_image']) ? $home_settings[0]['home_banner_image'] : null;
        $banner_title = !empty($home_settings[0]['banner_title']) ? $home_settings[0]['banner_title'] : 'Hire expert freelancers';
        $banner_subtitle = !empty($home_settings[0]['banner_subtitle']) ? $home_settings[0]['banner_subtitle'] : 'for any job, Online';
        $banner_description = !empty($home_settings[0]['banner_description']) ? $home_settings[0]['banner_description'] : null;
        $banner_video_link = !empty($home_settings[0]['video_link']) ? $home_settings[0]['video_link'] : null;
        $banner_video_title = !empty($home_settings[0]['video_title']) ? $home_settings[0]['video_title'] : null;
        $banner_video_desc = !empty($home_settings[0]['video_desc']) ? $home_settings[0]['video_desc'] : null;
        $section_bg = !empty($section_settings[0]['section_bg']) ? $section_settings[0]['section_bg'] : null;
        $company_title = !empty($section_settings[0]['company_title']) ? $section_settings[0]['company_title'] : null;
        $company_desc = !empty($section_settings[0]['company_desc']) ? $section_settings[0]['company_desc'] : null;
        $company_url = !empty($section_settings[0]['company_url']) ? $section_settings[0]['company_url'] : null;
        $freelancer_title = !empty($section_settings[0]['freelancer_title']) ? $section_settings[0]['freelancer_title'] : null;
        $freelancer_desc = !empty($section_settings[0]['freelancer_desc']) ? $section_settings[0]['freelancer_desc'] : null;
        $freelancer_url = !empty($section_settings[0]['freelancer_url']) ? $section_settings[0]['freelancer_url'] : null;
        $download_app_img = !empty($section_settings[0]['download_app_img']) ? $section_settings[0]['download_app_img'] : null;
        $app_title = !empty($section_settings[0]['app_title']) ? $section_settings[0]['app_title'] : null;
        $app_subtitle = !empty($section_settings[0]['app_subtitle']) ? $section_settings[0]['app_subtitle'] : null;
        $app_desc = $this->settings::where('meta_key', 'app_desc')->select('meta_value')->pluck('meta_value')->first();
        $app_android_link = $this->settings::where('meta_key', 'app_android_link')->select('meta_value')->pluck('meta_value')->first();
        $app_ios_link = $this->settings::where('meta_key', 'app_ios_link')->select('meta_value')->pluck('meta_value')->first();
        $cat_sec_title = !empty($section_settings[0]['cat_sec_title']) ? $section_settings[0]['cat_sec_title'] : null;
        $cat_sec_subtitle = !empty($section_settings[0]['cat_sec_subtitle']) ? $section_settings[0]['cat_sec_subtitle'] : null;
        return view(
            'back-end.admin.home-page-settings.index',
            compact(
                'banner_title', 'banner_subtitle', 'banner_description',
                'banner_video_link', 'banner_video_title', 'banner_video_desc',
                'banner_bg', 'banner_bg_image', 'company_title', 'company_desc',
                'company_url', 'freelancer_title', 'freelancer_desc',
                'freelancer_url', 'section_bg', 'download_app_img',
                'app_title', 'app_subtitle', 'app_desc', 'app_android_link',
                'app_ios_link', 'cat_sec_title', 'cat_sec_subtitle'
            )
        );
    }

    /**
     * Store Email Settings
     *
     * @param mixed $request get req attributes
     *
     * @access public
     *
     * @return View
     */
    public function storeEmailSettings(Request $request)
    {
        $server = Helper::worketicIsDemoSiteAjax();
        if (!empty($server)) {
            $response['type'] = 'error';
            $response['message'] = $server->getData()->message;
            return $response;
        }
        $json = array();
        if (!empty($request['email_data'])) {
            $store_email_settings
                = $this->settings->saveEmailSettings($request['email_data']);
            if ($store_email_settings == "success") {
                $json['type'] = 'success';
                $json['progressing'] = trans('lang.saving');
                $json['message'] = trans('lang.settings_saved');
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = trans('lang.something_wrong');
                return $json;
            }
        }
    }


    /**
     * Store home settings
     *
     * @param mixed $request get req attributes
     *
     * @access public
     *
     * @return View
     */
    public function storeHomeSettings(Request $request)
    {
        $server = Helper::worketicIsDemoSiteAjax();
        if (!empty($server)) {
            $response['type'] = 'error';
            $response['message'] = $server->getData()->message;
            return $response;
        }
        $json = array();
        if (!empty($request)) {
            $store_home_settings = SiteManagement::saveHomeSettings($request['home'], $request);
            if ($store_home_settings == "success") {
                $json['type'] = 'success';
                $json['progressing'] = trans('lang.saving');
                $json['message'] = trans('lang.settings_saved');
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = trans('lang.something_wrong');
                return $json;
            }
        }
    }

    /**
     * Store section settings
     *
     * @param mixed $request get req attributes
     *
     * @access public
     *
     * @return View
     */
    public function storeSectionSettings(Request $request)
    {
        $server = Helper::worketicIsDemoSiteAjax();
        if (!empty($server)) {
            $response['type'] = 'error';
            $response['message'] = $server->getData()->message;
            return $response;
        }
        $json = array();
        if (!empty($request)) {
            $store_home_settings = SiteManagement::saveSectionSettings($request['section'], $request);
            if ($store_home_settings == "success") {
                $json['type'] = 'success';
                $json['progressing'] = trans('lang.saving');
                $json['message'] = trans('lang.settings_saved');
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = trans('lang.something_wrong');
                return $json;
            }
        }
    }

    /**
     * Store general settings
     *
     * @param mixed $request get req attributes
     *
     * @access public
     *
     * @return View
     */
    public function storeGeneralSettings(Request $request)
    {
        $server = Helper::worketicIsDemoSiteAjax();
        if (!empty($server)) {
            $response['type'] = 'error';
            $response['message'] = $server->getData()->message;
            return $response;
        }
        $json = array();
        if (!empty($request['settings'])) {
            $store_email_settings
                = $this->settings->saveSettings($request['settings']);
            if ($store_email_settings == "success") {
                $json['type'] = 'success';
                $json['progressing'] = trans('lang.saving');
                $json['message'] = trans('lang.settings_saved');
                return $json;
            } elseif ($store_email_settings == "lang_not_found") {
                $json['type'] = 'error';
                $json['message'] = trans('lang.lang_not_found');
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = trans('lang.something_wrong');
                return $json;
            }
        }
    }

    /**
     * Store dashboard icons
     *
     * @param mixed $request get req attributes
     *
     * @access public
     *
     * @return View
     */
    public function storeDashboardIcons(Request $request)
    {
        $server = Helper::worketicIsDemoSiteAjax();
        if (!empty($server)) {
            $response['type'] = 'error';
            $response['message'] = $server->getData()->message;
            return $response;
        }
        $json = array();
        if (!empty($request)) {
            $upload_icons
                = $this->settings->saveIcons($request);
            if ($upload_icons == "success") {
                $json['type'] = 'success';
                $json['progressing'] = trans('lang.saving');
                $json['message'] = trans('lang.settings_saved');
                return $json;
            } elseif ($upload_icons == "lang_not_found") {
                $json['type'] = 'error';
                $json['message'] = trans('lang.lang_not_found');
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = trans('lang.something_wrong');
                return $json;
            }
        }
    }

    /**
     * Store theme color settings
     *
     * @param mixed $request get req attributes
     *
     * @access public
     *
     * @return View
     */
    public function storeThemeStylingSettings(Request $request)
    {
        $server = Helper::worketicIsDemoSiteAjax();
        if (!empty($server)) {
            $response['type'] = 'error';
            $response['message'] = $server->getData()->message;
            return $response;
        }
        $json = array();
        if (!empty($request)) {
            $store_theme_settings
                = $this->settings->saveThemeStylingSettings($request);
            if ($store_theme_settings == "success") {
                $json['type'] = 'success';
                $json['progressing'] = trans('lang.saving');
                $json['message'] = trans('lang.settings_saved');
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = trans('lang.something_wrong');
                return $json;
            }
        }
    }

    /**
     * Store Footer Settings
     *
     * @param mixed $request get req attributes
     *
     * @access public
     *
     * @return View
     */
    public function storeFooterSettings(Request $request)
    {
        $server = Helper::worketicIsDemoSiteAjax();
        if (!empty($server)) {
            $response['type'] = 'error';
            $response['message'] = $server->getData()->message;
            return $response;
        }
        $json = array();
        if (!empty($request['footer'])) {
            $footer_settings = $this->settings->saveFooterSettings($request['footer']);
            if ($footer_settings == "success") {
                $json['type'] = 'success';
                $json['progressing'] = trans('lang.saving');
                $json['message'] = trans('lang.settings_saved');
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = trans('lang.something_wrong');
                return $json;
            }
        } else {
            $json['type'] = 'error';
            $json['message'] = trans('lang.something_wrong');
            return $json;
        }
    }

    /**
     * Store social settings
     *
     * @param mixed $request get req attributes
     *
     * @access public
     *
     * @return View
     */
    public function storeSocialSettings(Request $request)
    {
        $server = Helper::worketicIsDemoSiteAjax();
        if (!empty($server)) {
            $response['type'] = 'error';
            $response['message'] = $server->getData()->message;
            return $response;
        }
        $json = array();
        if (!empty($request['social'])) {
            $social_settings = $this->settings->saveSocialSettings($request['social']);
            if ($social_settings == "success") {
                $json['type'] = 'success';
                $json['progressing'] = trans('lang.saving');
                $json['message'] = trans('lang.settings_saved');
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = trans('lang.something_wrong');
                return $json;
            }
        } else {
            $json['type'] = 'error';
            $json['message'] = trans('lang.something_wrong');
            return $json;
        }
    }

    /**
     * Store search menu.
     *
     * @param mixed $request get req attributes
     *
     * @access public
     *
     * @return View
     */
    public function storeSearchMenu(Request $request)
    {
        $server = Helper::worketicIsDemoSiteAjax();
        if (!empty($server)) {
            $response['type'] = 'error';
            $response['message'] = $server->getData()->message;
            return $response;
        }
        $this->validate(
            $request,
            [
                'menu_title' => 'required',
            ]
        );
        $json = array();
        if (!empty($request)) {
            $search_menu = $this->settings->saveSearchMenu($request);
            if ($search_menu['type'] == "success") {
                $json['type'] = 'success';
                $json['progressing'] = trans('lang.saving');
                $json['message'] = trans('lang.settings_saved');
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = trans('lang.all_required');
                return $json;
            }
        } else {
            $json['type'] = 'error';
            $json['message'] = trans('lang.all_required');
            return $json;
        }
    }

    /**
     * Store commision settings
     *
     * @param mixed $request get req attributes
     *
     * @access public
     *
     * @return View
     */
    public function storeCommisionSettings(Request $request)
    {
        $server = Helper::worketicIsDemoSiteAjax();
        if (!empty($server)) {
            $response['type'] = 'error';
            $response['message'] = $server->getData()->message;
            return $response;
        }
        $json = array();
        if (!empty($request['payment'])) {
            $default_settings = $this->settings->saveCommisionSettings($request['payment']);
            if ($default_settings == "success") {
                $json['type'] = 'success';
                $json['progressing'] = trans('lang.saving');
                $json['message'] = trans('lang.settings_saved');
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = trans('lang.something_wrong');
                return $json;
            }
        } else {
            $json['type'] = 'error';
            $json['message'] = trans('lang.something_wrong');
            return $json;
        }
    }

    /**
     * Store payment settings
     *
     * @param mixed $request get req attributes
     *
     * @access public
     *
     * @return View
     */
    public function storePaymentSettings(Request $request)
    {
        $server = Helper::worketicIsDemoSiteAjax();
        if (!empty($server)) {
            $response['type'] = 'error';
            $response['message'] = $server->getData()->message;
            return $response;
        }
        $this->validate(
            $request,
            [
                'client_id' => 'required',
                'paypal_password' => 'required',
                'paypal_secret' => 'required',
            ]
        );
        $json = array();
        if (!empty($request)) {
            $default_settings = $this->settings->savePaymentSettings($request);
            if ($default_settings == "success") {
                $json['type'] = 'success';
                $json['progressing'] = trans('lang.saving');
                $json['message'] = trans('lang.settings_saved');
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = trans('lang.something_wrong');
                return $json;
            }
        } else {
            $json['type'] = 'error';
            $json['message'] = trans('lang.something_wrong');
            return $json;
        }
    }

    /**
     * Store payment settings
     *
     * @param mixed $request get req attributes
     *
     * @access public
     *
     * @return View
     */
    public function storeStripeSettings(Request $request)
    {
        $server = Helper::worketicIsDemoSiteAjax();
        if (!empty($server)) {
            $response['type'] = 'error';
            $response['message'] = $server->getData()->message;
            return $response;
        }
        $this->validate(
            $request,
            [
                'stripe_key' => 'required',
                'stripe_secret' => 'required',
            ]
        );
        $json = array();
        if (!empty($request)) {
            $default_settings = $this->settings->saveStripeSettings($request);
            if ($default_settings == "success") {
                $json['type'] = 'success';
                $json['progressing'] = trans('lang.saving');
                $json['message'] = trans('lang.settings_saved');
                return $json;
            } else {
                $json['type'] = 'error';
                $json['message'] = trans('lang.something_wrong');
                return $json;
            }
        } else {
            $json['type'] = 'error';
            $json['message'] = trans('lang.something_wrong');
            return $json;
        }
    }

    /**
     * Upload Image to temporary folder.
     *
     * @param mixed  $request   request attributes
     * @param string $file_name getfilename
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadTempImage(Request $request, $file_name = '')
    {
        $path = Helper::PublicPath() . '/uploads/settings/temp/';
        if (!empty($request[$file_name])) {
            Helper::uploadSingleTempImage($path, $request[$file_name]);
        }
    }

    /**
     * Import Demo content.
     *
     * @return \Illuminate\Http\Response
     */
    public function importDemo()
    {
        $server_verification = Helper::worketicIsDemoSite();
        if (!empty($server_verification)) {
            Session::flash('error', $server_verification);
            return redirect()->to('admin/settings');
        }
        \Artisan::call('migrate:fresh');
        \Artisan::call('db:seed');
        return redirect()->to('/');
    }

    /**
     * Remove Demo content.
     *
     * @return \Illuminate\Http\Response
     */
    public function removeDemoContent()
    {
        $server_verification = Helper::worketicIsDemoSite();
        if (!empty($server_verification)) {
            Session::flash('error', $server_verification);
            return Redirect::back();
        }
        \Artisan::call('migrate:fresh');
        \Artisan::call('db:seed', ['--class' => 'AdminSeeder', '--force' => true]);
        \Artisan::call('db:seed', ['--class' => 'AdminProfileSeeder', '--force' => true]);
        \Artisan::call('db:seed', ['--class' => 'RoleTableSeeder', '--force' => true]);
        \Artisan::call('db:seed', ['--class' => 'AdminModelHasRoleSeeder', '--force' => true]);
        \Artisan::call('db:seed', ['--class' => 'EmailTypeSeeder', '--force' => true]);
        \Artisan::call('db:seed', ['--class' => 'EmailTemplateSeeder', '--force' => true]);
        \Artisan::call('db:seed', ['--class' => 'TrialPackageSeeder', '--force' => true]);
        \Artisan::call('db:seed', ['--class' => 'TrialInvoiceSeeder', '--force' => true]);
        return redirect()->to('/');
    }

    /**
     * Clear select cache of the app.
     *
     * @param boolean $request $req
     *
     * @return \Illuminate\Http\Response
     */
    public function clearCache(Request $request)
    {
        $json = array();
        if ($request['clear_cache'] == true) {
            \Artisan::call('config:cache');
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
        }
        if ($request['clear_views'] == true) {
            \Artisan::call('view:clear');
        }
        if ($request['clear_routes'] == true) {
            \Artisan::call('route:clear');
        }
        $json['type'] = 'success';
        return $json;
    }

    /**
     * Remove all cache of the app.
     *
     * @return \Illuminate\Http\Response
     */
    public function clearAllCache()
    {
        $json = array();
        \Artisan::call('optimize:clear');
        $json['type'] = 'success';
        return $json;
    }

    /**
     * Remove all cache of the app.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPageOption(Request $request)
    {
        $json = array();
        $settings = DB::table('site_managements')->select('meta_value')->where('meta_key', 'show-page-'.$request['page_id'])->get()->first();

        if (!empty($settings)) {
            $json['type'] = 'success';
            $json['show_page'] = $settings->meta_value;
        } else {
            $json['type'] = 'error';
        }
        return $json;
    }

    /**
     * Store registration settings
     *
     * @param mixed $request get req attributes
     *
     * @access public
     *
     * @return View
     */
    public function storeRegistrationSettings(Request $request)
    {
        $server = Helper::worketicIsDemoSiteAjax();
        if (!empty($server)) {
            $response['type'] = 'error';
            $response['message'] = $server->getData()->message;
            return $response;
        }
        if (!empty($request)) {
            $reg_settings = $this->settings->saveRegistrationSettings($request);
            if ($reg_settings == "success") {
                Session::flash('message', trans('lang.settings_saved'));
                return Redirect::back();
            } elseif ($reg_settings == "error") {
                Session::flash('error', trans('lang.all_required'));
                return Redirect::back();
            }
        } else {
            Session::flash('error', trans('lang.something_wrong'));
            return Redirect::back();
        }
    }

    /**
     * Get section display settings
     *
     * @access public
     *
     * @return View
     */
    public function getSectionDisplaySetting()
    {
        $json = array();
        $section_settings = !empty(SiteManagement::getMetaValue('section_settings')) ? SiteManagement::getMetaValue('section_settings') : array();
        if (!empty($section_settings[0]['cat_section_display'])) {
            if ($section_settings[0]['cat_section_display'] == 'true') {
                $json['cat_section_display'] = 'true';
            }
        }
        if (!empty($section_settings[0]['home_section_display'])) {
            if ($section_settings[0]['home_section_display'] == 'true') {
                $json['home_section_display'] = 'true';
            }
        }
        if (!empty($section_settings[0]['app_section_display'])) {
            if ($section_settings[0]['app_section_display'] == 'true') {
                $json['app_section_display'] = 'true';
            }
        }
        return $json;
    }

    /**
     * Get section display settings
     *
     * @access public
     *
     * @return View
     */
    public function getThemeColorDisplaySetting()
    {
        $json = array();
        $settings = !empty(SiteManagement::getMetaValue('settings')) ? SiteManagement::getMetaValue('settings') : array();
        if (!empty($settings[0]['enable_theme_color'])) {
            if ($settings[0]['enable_theme_color'] == 'true') {
                $json['enable_theme_color'] = 'true';
            }
        }
        return $json;
    }

    /**
     * Get chat display setting
     *
     * @access public
     *
     * @return View
     */
    public function getchatDisplaySetting()
    {
        $json = array();
        $settings = !empty(SiteManagement::getMetaValue('settings')) ? SiteManagement::getMetaValue('settings') : array();
        if (!empty($settings[0]['chat_display'])) {
            if ($settings[0]['chat_display'] == 'true') {
                $json['chat_display'] = 'true';
            }
        }
        return $json;
    }
}
