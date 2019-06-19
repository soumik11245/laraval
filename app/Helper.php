<?php

/**
 * Class Helper
 *
 * @category Worketic
 *
 * @package Worketic
 * @author  Amentotech <theamentotech@gmail.com>
 * @license http://www.amentotech.com Amentotech
 * @link    http://www.amentotech.com
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;
use File;
use Storage;
use Spatie\Permission\Models\Role;
use DB;
use function GuzzleHttp\json_encode;
use APP\Category;
use APP\Location;
use Auth;
use App\Item;
use App\Payout;
use App\Proposal;
use App\User;
use App\SiteManagement;
use App\Badge;
use Illuminate\Support\Arr;

/**
 * Class Helper
 *
 */
class Helper extends Model
{
    /**
     * Set slug before saving in DB
     *
     * @access public
     *
     * @return array
     */
    public static function getGender()
    {
        $gender = ['male' => 'Male', 'female' => 'Female'];
        return $gender;
    }

    /**
     * Generate random code
     *
     * @param integer $limit Limit of numbers
     *
     * @access public
     *
     * @return array
     */
    public static function generateRandomCode($limit)
    {
        if (!empty($limit) && is_numeric($limit)) {
            return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
        }
    }

    /**
     * Get employees list
     *
     * @access public
     *
     * @return array
     */
    public static function getEmployeesList()
    {
        $list = array(
            '1' => array(
                'title' => trans('lang.employee_list.just_me'),
                'search_title' => 'Less Than Two',
                'value' => 1,
            ),
            '2' => array(
                'title' => trans('lang.employee_list.2_9'),
                'search_title' => 'Less Than 10',
                'value' => 10,
            ),
            '3' => array(
                'title' => trans('lang.employee_list.10_99'),
                'search_title' => 'Less Than 100',
                'value' => 100,
            ),
            '4' => array(
                'title' => trans('lang.employee_list.100_499'),
                'search_title' => 'Less Than 500',
                'value' => 500,
            ),
            '5' => array(
                'title' => trans('lang.employee_list.500_100'),
                'search_title' => 'Less Than 1000',
                'value' => 1000,
            ),
            '6' => array(
                'title' => trans('lang.employee_list.500_1000'),
                'search_title' => 'More Than 1000',
                'value' => 5000,
            ),
        );
        return $list;
    }

    /**
     * Get location flag
     *
     * @param image $image location flag
     *
     * @access public
     *
     * @return string
     */
    public static function getLocationFlag($image)
    {
        if (!empty($image)) {
            return '/uploads/locations/' . $image;
        } else {
            return 'uploads/locations/img-09.png';
        }
    }

    /**
     * Get category image
     *
     * @param image $image location flag
     *
     * @access public
     *
     * @return string
     */
    public static function getCategoryImage($image)
    {
        if (!empty($image)) {
            return '/uploads/categories/' . $image;
        } else {
            return 'uploads/categories/img-09.png';
        }
    }

    /**
     * Get badge Image
     *
     * @param image $image badge Image
     *
     * @access public
     *
     * @return string
     */
    public static function getBadgeImage($image)
    {
        if (!empty($image)) {
            return '/uploads/badges/' . $image;
        } else {
            return '';
        }
    }

    // /**
    //  * Get background image
    //  *
    //  * @param image $image location flag
    //  *
    //  * @access public
    //  *
    //  * @return string
    //  */
    // public static function getBackgroundImage($image)
    // {
    //     if (!empty($image)) {
    //         return '/uploads/settings/home/' . $image;
    //     } else {
    //         return 'images/banner-bg.jpg';
    //     }
    // }

    /**
     * Get banner image
     *
     * @param image $image location flag
     * @param image $size  image size
     *
     * @access public
     *
     * @return string
     */
    public static function getBannerImage($image, $size = "")
    {
        // if (!empty($image)) {
        //     if (!empty($size)) {
        //         return '/uploads/settings/home/' . $size . '-' . $image;
        //     } else {
        //         return '/uploads/settings/home/' . $image;
        //     }
        // } else {
        //     return 'images/banner-img.png';
        // }
    }

    /**
     * Get download app image
     *
     * @param image $image download app image
     *
     * @access public
     *
     * @return string
     */
    public static function getDownloadAppImage($image)
    {
        if (!empty($image)) {
            return '/uploads/settings/home/' . $image;
        } else {
            return 'images/mobile-img.png';
        }
    }

    /**
     * Get Header logo image
     *
     * @param image $image header logo
     *
     * @access public
     *
     * @return string
     */
    public static function getHeaderLogo($image)
    {
        if (!empty($image)) {
            return '/uploads/settings/general/' . $image;
        } else {
            return 'images/logo.png';
        }
    }

    /**
     * Get footer logo image
     *
     * @param image $image download app image
     *
     * @access public
     *
     * @return string
     */
    public static function getFooterLogo($image)
    {
        if (!empty($image)) {
            return '/uploads/settings/footer/' . $image;
        } else {
            return 'images/flogo.png';
        }
    }

    /**
     * Store Temporary profile images
     *
     * @param mixed $temp_path Temporary Path.
     * @param mixed $image     Image.
     * @param mixed $file_name file name
     *
     * @return json response
     */
    public static function uploadTempImage($temp_path, $image, $file_name = "")
    {
        $json = array();
        if (!empty($image)) {
            $file_original_name = $image->getClientOriginalName();
            $parts = explode('.', $file_original_name);
            $extension = end($parts);
            $extension = $image->getClientOriginalExtension();
            if ($extension === "jpg" || $extension === "png") {
                $file_original_name = !empty($file_name) ? $file_name : $file_original_name;
                // create directory if not exist.
                if (!file_exists($temp_path)) {
                    File::makeDirectory($temp_path, 0755, true, true);
                }
                // generate small image size
                $small_img = Image::make($image);
                $small_img->fit(
                    36,
                    36,
                    function ($constraint) {
                        $constraint->upsize();
                    }
                );
                $small_img->save($temp_path . '/small-' . $file_original_name);
                // generate medium image size
                $medium_img = Image::make($image);
                $medium_img->fit(
                    100,
                    100,
                    function ($constraint) {
                        $constraint->upsize();
                    }
                );
                $medium_img->save($temp_path . '/medium-' . $file_original_name);
                // save original image size
                $img = Image::make($image);
                $img->save($temp_path . '/' . $file_original_name);
                $json['message'] = trans('lang.img_uploaded');
                $json['type'] = 'success';
                return $json;
            } else {
                $json['message'] = trans('lang.img_jpg_png');
                $json['type'] = 'error';
                return $json;
            }
        } else {
            $json['message'] = trans('lang.image not found');
            $json['type'] = 'error';
            return $json;
        }
    }

    /**
     * Store Temporary images
     *
     * @param mixed $temp_path  Temporary Path.
     * @param mixed $image      Image.
     * @param mixed $file_name  File Name.
     * @param mixed $image_size Image Size.
     *
     * @return json response
     */
    public static function uploadTempImageWithSize($temp_path, $image, $file_name = "", $image_size = array())
    {
        $json = array();
        if (!empty($image)) {
            $file_original_name = $image->getClientOriginalName();
            $parts = explode('.', $file_original_name);
            $extension = end($parts);
            $extension = $image->getClientOriginalExtension();
            if ($extension === "jpg" || $extension === "png") {
                $file_original_name = !empty($file_name) ? $file_name : $file_original_name;
                // create directory if not exist.
                if (!file_exists($temp_path)) {
                    File::makeDirectory($temp_path, 0755, true, true);
                }
                if (!empty($image_size)) {
                    foreach ($image_size as $key => $size) {
                        $small_img = Image::make($image);
                        $small_img->fit(
                            $size['width'],
                            $size['height'],
                            function ($constraint) {
                                $constraint->upsize();
                            }
                        );
                        $small_img->save($temp_path . $key . '-' . $file_original_name);
                    }
                }
                // save original image size
                $img = Image::make($image);
                $img->save($temp_path . '/' . $file_original_name);
                $json['message'] = trans('lang.img_uploaded');
                $json['type'] = 'success';
                return $json;
            } else {
                $json['message'] = trans('lang.img_jpg_png');
                $json['type'] = 'error';
                return $json;
            }
        } else {
            $json['message'] = trans('lang.image not found');
            $json['type'] = 'error';
            return $json;
        }
    }

    /**
     * Upload image to new path
     *
     * @param mixed $image    Image.
     * @param mixed $old_path Old path.
     * @param mixed $new_path New path.
     * @param mixed $counter  Counter.
     *
     * @return $json response
     */
    public static function uploadTempToNewPath($image, $old_path, $new_path, $counter = '')
    {
        if (!empty($image)) {
            $filename = $image;
            if (Storage::disk('local')->exists($old_path . '/' . $image)) {
                if (!file_exists($new_path)) {
                    File::makeDirectory($new_path, 0755, true, true);
                }
                $filename = time() . $counter . '-' . $image;
                Storage::move($old_path . '/' . $image, $new_path . '/' . $filename);
                Storage::move($old_path . '/small-' . $image, $new_path . '/small-' . $filename);
                Storage::move($old_path . '/medium-' . $image, $new_path . '/medium-' . $filename);
            }
            return $filename;
        }
    }

    /**
     * Get English Level List
     *
     * @param string $key key
     *
     * @access public
     *
     * @return array
     */
    public static function getEnglishLevelList($key = "")
    {
        $list = array(
            'basic'             => trans('lang.basic'),
            'conversational'    => trans('lang.conversational'),
            'fluent'            => trans('lang.fluent'),
            'native'            => trans('lang.native'),
            'professional'      => trans('lang.professional'),
        );
        if (!empty($key) && array_key_exists($key, $list)) {
            return $list[$key];
        } else {
            return $list;
        }
    }

    /**
     * Get Project List
     *
     * @param string $key key
     *
     * @access public
     *
     * @return array
     */
    public static function getProjectLevel($key = "")
    {
        $list = array(
            'basic'     => trans('lang.project_level.basic'),
            'medium'    => trans('lang.project_level.medium'),
            'expensive' => trans('lang.project_level.expensive'),
        );
        if (!empty($key) && array_key_exists($key, $list)) {
            return $list[$key];
        } else {
            return $list;
        }
    }

    /**
     * Get Project Type
     *
     * @param string $key key
     *
     * @access public
     *
     * @return array
     */
    public static function getProjectType($key = "")
    {
        $list = array(
            'projects' => trans('lang.projecttype.projects'),
            'hourly'  => trans('lang.projecttype.hourly'),
            'fixed' => trans('lang.projecttype.fixed'),
        );
        if (!empty($key) && array_key_exists($key, $list)) {
            return $list[$key];
        } else {
            return $list;
        }
    }

    /**
     * Get Project Status
     *
     * @param string $key key
     *
     * @access public
     *
     * @return array
     */
    public static function getProjectStatus($key = "")
    {
        $list = array(
            'completed' => trans('lang.project_status.completed'),
            'cancelled' => trans('lang.project_status.cancelled'),
            'hired'     => trans('lang.project_status.hired'),
        );
        if (!empty($key) && array_key_exists($key, $list)) {
            return $list[$key];
        } else {
            return $list;
        }
    }

    /**
     * Get Job Duration List
     *
     * @param string $key key
     *
     * @access public
     *
     * @return array
     */
    public static function getJobDurationList($key = "")
    {
        $list = array(
            'weekly' => trans('lang.job_duration.weekly'),
            'monthly' => trans('lang.job_duration.monthly'),
            'three_month' => trans('lang.job_duration.three_month'),
            'six_month' => trans('lang.job_duration.six_month'),
            'more_than_six' => trans('lang.job_duration.more_than_six'),
        );
        if (!empty($key) && array_key_exists($key, $list)) {
            return $list[$key];
        } else {
            return $list;
        }
    }

    /**
     * Get Job Types List
     *
     * @param string $key key
     *
     * @access public
     *
     * @return array
     */
    public static function getJobTypesList($key = "")
    {
        $list = array(
            'all' => trans('lang.jobtype.all'),
            'featured' => trans('lang.jobtype.featured'),
            'fixed' => trans('lang.jobtype.fixed'),
            'hourly' => trans('lang.jobtype.hourly'),
        );
        if (!empty($key) && array_key_exists($key, $list)) {
            return $list[$key];
        } else {
            return $list;
        }
    }

    /**
     * Get Hourly Rate
     *
     * @param string $key key
     *
     * @access public
     *
     * @return array
     */
    public static function getHourlyRate($key = "")
    {
        $list = array(
            '0-5' => trans('lang.freelancer_hourly_rate.0_5'),
            '5-10' => trans('lang.freelancer_hourly_rate.5_10'),
            '10-20' => trans('lang.freelancer_hourly_rate.10_20'),
            '20-30' => trans('lang.freelancer_hourly_rate.20_30'),
            '30-40' => trans('lang.freelancer_hourly_rate.30_40'),
            '40-50' => trans('lang.freelancer_hourly_rate.40_50'),
            '50-60' => trans('lang.freelancer_hourly_rate.50_60'),
            '60-70' => trans('lang.freelancer_hourly_rate.60_70'),
            '70-80' => trans('lang.freelancer_hourly_rate.70_80'),
            '90-0' => trans('lang.freelancer_hourly_rate.90_0'),
        );
        if (!empty($key) && array_key_exists($key, $list)) {
            return $list[$key];
        } else {
            return $list;
        }
    }

    /**
     * Job Completion Time
     *
     * @param string $key key
     *
     * @access public
     *
     * @return array
     */
    public static function getJobCompletionTimeList($key = "")
    {
        $list = array(
            'one_month' => trans('lang.job_completion.one_month'),
            'two_month' => trans('lang.job_completion.two_month'),
            'three_month' => trans('lang.job_completion.three_month'),
            'four_month' => trans('lang.job_completion.four_month'),
        );
        if (!empty($key) && array_key_exists($key, $list)) {
            return $list[$key];
        } else {
            return $list;
        }
    }

    /**
     * Get Freelancer Level
     *
     * @param string $key key
     *
     * @access public
     *
     * @return array
     */
    public static function getFreelancerLevelList($key = "")
    {
        $list = array(
            'independent'       => trans('lang.freelancer_level.independent'),
            'agency'            => trans('lang.freelancer_level.agency'),
            'rising_talent'     => trans('lang.freelancer_level.rising_talent'),
        );
        if (!empty($key) && array_key_exists($key, $list)) {
            return $list[$key];
        } else {
            return $list;
        }
    }

    /**
     * Get Report Reasons
     *
     * @access public
     *
     * @return array
     */
    public static function getReportReasons()
    {
        $list = array(
            '1' => array(
                'title' => trans('lang.report_reasons.fake'),
                'value' => 'fake',
            ),
            '2' => array(
                'title' => trans('lang.report_reasons.behaviour'),
                'value' => 'behavior',
            ),
            '3' => array(
                'title' => trans('lang.report_reasons.other'),
                'value' => 'Other',
            ),
        );
        return $list;
    }

    /**
     * Get Delete Acc Reasons
     *
     * @access public
     *
     * @return array
     */
    public static function getDeleteAccReason($key = "")
    {
        $list = array(
            'not_satisfied' => trans('lang.del_acc_reason.not_satisfied'),
            'not_good_support' => trans('lang.del_acc_reason.no_good_supp'),
            'Others' => trans('lang.del_acc_reason.others'),
        );
        if (!empty($key) && array_key_exists($key, $list)) {
            return $list[$key];
        } else {
            return $list;
        }
    }

    /**
     * Get Package Duration List
     *
     * @param string $key key
     *
     * @access public
     *
     * @return array
     */
    public static function getPackageDurationList($key = "")
    {
        $list = array(
            '10' => trans('lang.pckge_duration.10'),
            '30' => trans('lang.pckge_duration.30'),
            '360' => trans('lang.pckge_duration.360'),
        );
        if (!empty($key) && array_key_exists($key, $list)) {
            return $list[$key];
        } else {
            return $list;
        }
    }

    /**
     * Get Freelancer Badge
     *
     * @param string $key key
     *
     * @access public
     *
     * @return array
     */
    public static function getFreelancerBadgeList($key = "")
    {
        $list = array(
            'gold'   => trans('lang.badge.gold'),
            'silver' => trans('lang.badge.silver'),
            'brown'  => trans('lang.badge.brown'),
        );
        if (!empty($key) && array_key_exists($key, $list)) {
            return $list[$key];
        } else {
            return $list;
        }
    }

    /**
     * Upload multiple attachments.
     *
     * @param mixed $uploadedFile uploaded file
     * @param mixed $path         path of file
     *
     * @return relation
     */
    public static function uploadTempMultipleAttachments($uploadedFile, $path)
    {
        if (!file_exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }
        $filename = $uploadedFile->getClientOriginalName();
        Storage::disk('local')->putFileAs(
            $path,
            $uploadedFile,
            $filename
        );
        return 'success';
    }

    /**
     * Get username
     *
     * @param integer $user_id ID
     *
     * @access public
     *
     * @return array
     */
    public static function getUserName($user_id)
    {
        if (!empty($user_id)) {
            return User::find($user_id)->first_name . ' ' . User::find($user_id)->last_name;
        } else {
            return '';
        }
    }

    /**
     * Get role name by ID
     *
     * @param integer $role_id ID
     *
     * @access public
     *
     * @return array
     */
    public static function getRoleName($role_id)
    {
        return Role::find($role_id)->name;
    }

    /**
     * Get package options
     *
     * @param string $role Role
     *
     * @access public
     *
     * @return array
     */
    public static function getPackageOptions($role)
    {
        if (!empty($role)) {
            if ($role == 'employer') {
                $list = array(
                    '0' => trans('lang.emp_pkg_opt.price'),
                    '1' => trans('lang.emp_pkg_opt.no_of_jobs'),
                    '2' => trans('lang.emp_pkg_opt.no_of_featured_job'),
                    '3' => trans('lang.emp_pkg_opt.pkg_duration'),
                    '4' => trans('lang.emp_pkg_opt.banner'),
                    '5' => trans('lang.emp_pkg_opt.pvt_cht'),
                );
            } elseif ($role == 'freelancer') {
                $list = array(
                    '0' => trans('lang.freelancer_pkg_opt.price'),
                    '1' => trans('lang.freelancer_pkg_opt.no_of_credits'),
                    '2' => trans('lang.freelancer_pkg_opt.no_of_skills'),
                    '3' => trans('lang.freelancer_pkg_opt.pkg_duration'),
                    '4' => trans('lang.freelancer_pkg_opt.badge'),
                    '5' => trans('lang.freelancer_pkg_opt.banner'),
                    '6' => trans('lang.freelancer_pkg_opt.pvt_cht'),
                );
            }
            return $list;
        }
    }

    /**
     * Get role by userID
     *
     * @param integer $user_id UserID
     *
     * @access public
     *
     * @return array
     */
    public static function getRoleByUserID($user_id)
    {
        $role = DB::table('model_has_roles')->select('role_id')->where('model_id', $user_id)
            ->first();
        return $role->role_id;
    }

    /**
     * Get role by roleID
     *
     * @param integer $role_id RoleID
     *
     * @access public
     *
     * @return array
     */
    public static function getRoleNameByRoleID($role_id)
    {
        $role = \Spatie\Permission\Models\Role::where('id', $role_id)
            ->first();
        if (!empty($role)) {
            return $role->name;
        } else {
            return '-';
        }
    }

    /**
     * Change the .env file Data.
     *
     * @param array $data array
     *
     * @return array
     */
    public static function changeEnv($data = array())
    {
        if (count($data) > 0) {

            // Read .env-file
            $env = file_get_contents(base_path() . '/.env');

            // Split string on every " " and write into array
            $env = preg_split('/\s+/', $env);;

            // Loop through given data
            foreach ((array)$data as $key => $value) {

                // Loop through .env-data
                foreach ($env as $env_key => $env_value) {

                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value, 2);

                    // Check, if new key fits the actual .env-key
                    if ($entry[0] == $key) {
                        // If yes, overwrite it with the new one
                        $env[$env_key] = $key . "=" . $value;
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            }

            // Turn the array back to an String
            $env = implode("\n", $env);

            // And overwrite the .env with the new data
            file_put_contents(base_path() . '/.env', $env);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Get search filters
     *
     * @param string $key key
     *
     * @access public
     *
     * @return array
     */
    public static function getSearchFilterList($key = "")
    {
        $list = array(
            '0' => array(
                'title' => trans('lang.search_filter_list.freelancer'),
                'value' => 'freelancer',
            ),
            '1' => array(
                'title' => trans('lang.search_filter_list.jobs'),
                'value' => 'job',
            ),
            '2' => array(
                'title' => trans('lang.search_filter_list.employers'),
                'value' => 'employer',
            ),
        );
        if (!empty($key) && array_key_exists($key, $list)) {
            return $list[$key];
        } else {
            return $list;
        }
    }

    /**
     * Get search filters
     *
     * @param string $type type
     *
     * @access public
     *
     * @return array
     */
    public static function getSearchableList($type)
    {
        $json = array();
        if ($type == 'freelancer') {
            $freelancs = User::role('freelancer')->select(
                DB::raw("CONCAT(users.first_name,' ',users.last_name) AS name"),
                "slug"
            )->get()->toArray();
            $json = $freelancs;
        }
        if ($type == 'employer') {
            $employers = User::role('employer')->select(
                DB::raw("CONCAT(users.first_name,' ',users.last_name) AS name"),
                "slug"
            )->get()->toArray();
            $json = $employers;
        }
        if ($type == 'job') {
            $jobs = DB::table("jobs")
                ->select(
                    "title AS name",
                    "slug"
                )->get()->toArray();
            $json = $jobs;
        }
        return $json;
    }

    /**
     * Get social media data
     *
     * @access public
     *
     * @return array
     */
    public static function getSocialData()
    {
        $social = array(
            'facebook' => array(
                'title' => trans('lang.social_icons.fb'),
                'color' => '#3b5999',
                'icon' => 'fa fa-facebook-f',
            ),
            'twitter' => array(
                'title' => trans('lang.social_icons.twitter'),
                'color' => '#55acee',
                'icon' => 'fab fa-twitter',
            ),
            'youtube' => array(
                'title' => trans('lang.social_icons.youtube'),
                'color' => '#0077B5',
                'icon' => 'fab fa-youtube',
            ),
            'instagram' => array(
                'title' => trans('lang.social_icons.insta'),
                'color' => '#dd4b39',
                'icon' => 'fab fa-instagram',
            ),
            'googleplus' => array(
                'title' => trans('lang.social_icons.gplus'),
                'color' => '#dd4b39',
                'icon' => 'fab fa-google-plus-g',
            )
        );
        return $social;
    }

    /**
     * Language list
     *
     * @param string $lang lang
     *
     * @access public
     *
     * @return array
     */
    public static function getTranslatedLang($lang="")
    {
        $languages = array(
            'en' => array(
                'code' => 'en',
                'title' => 'English',
            ),
            'de' => array(
                'code' => 'de',
                'title' => 'German',
            ),
            'tr' => array(
                'code' => 'tr',
                'title' => 'Turkish',
            ),
            'es' => array(
                'code' => 'es',
                'title' => 'Spanish',
            ),
            'pt' => array(
                'code' => 'pt',
                'title' => 'Portuguese',
            ),
            'zh' => array(
                'code' => 'zh',
                'title' => 'Chinese',
            ),
            'bn' => array(
                'code' => 'bn',
                'title' => 'Bengali',
            ),
            'fr' => array(
                'code' => 'fr',
                'title' => 'French',
            ),
            'ru' => array(
                'code' => 'ru',
                'title' => 'Russian',
            ),
            'UK' => array(
                'code' => 'UK',
                'title' => 'Ukrainian',
            ),
            'ja' => array(
                'code' => 'ja',
                'title' => 'Japanese',
            ),
        );

        if (!empty($lang) && array_key_exists($lang, $languages)) {
            return $languages[$lang];
        } else {
            return $languages;
        }
    }

    /**
     * Display socials
     *
     * @access public
     *
     * @return array
     */
    public static function displaySocials()
    {
        $output = "";
        $social_unserialize_array = SiteManagement::getMetaValue('socials');
        $social_list = Helper::getSocialData();
        if (!empty($social_unserialize_array)) {
            $output .= "<ul class='wt-socialiconssimple wt-socialiconfooter'>";
            foreach ($social_unserialize_array as $key => $value) {
                if (array_key_exists($value['title'], $social_list)) {
                    $socialList = $social_list[$value['title']];
                    $output .= "<li class='wt-{$value['title']}'><a href = '{$value["url"]}'><i class='fa {$socialList["icon"]}' ></i></a></li>";
                }
            }
            $output .= "</ul>";
        }
        echo $output;
    }

    /**
     * Get user profile image
     *
     * @param integer $user_id user_id
     *
     * @access public
     *
     * @return array
     */
    public static function getProfileImage($user_id)
    {
        $profile_image = User::find($user_id)->profile->avater;
        return !empty($profile_image) ? '/uploads/users/' . $user_id . '/' . $profile_image : '/images/user.jpg';
    }

    /**
     * Get user profile image
     *
     * @param integer $user_id user_id
     * @param integer $size    size
     *
     * @access public
     *
     * @return array
     */
    public static function getUserProfileBanner($user_id, $size = '')
    {
        $user = User::getUserRoleType($user_id);
        $profile_banner = User::find($user_id)->profile->banner;
        if (!empty($profile_banner)) {
            if (!empty($size)) {
                return '/uploads/users/' . $user_id . '/' . $size . '-' . $profile_banner;
            } else {
                return '/uploads/users/' . $user_id . '/' . $profile_banner;
            }
        } elseif ($user->role_type == 'freelancer') {
            if (!empty($size)) {
                if (file_exists('images/' . $size . '-frbanner-1920x400.jpg')) {
                    return 'images/' . $size . '-frbanner-1920x400.jpg';
                } else {
                    return 'images/frbanner-1920x400.jpg';
                }
            } else {
                return 'images/frbanner-1920x400.jpg';
            }
        } elseif ($user->role_type == 'employer') {
            if (!empty($size)) {
                if (file_exists('images/'.$size.'-e-1110x300.jpg')) {
                    return 'images/' . $size . '-e-1110x300.jpg';
                } else {
                    return 'images/e-1110x300.jpg';
                }
            } else {
                return 'images/e-1110x300.jpg';
            }
        }
    }


    /**
     * Get user profile image
     *
     * @param integer $user_id user_id
     *
     * @access public
     *
     * @return array
     */
    public static function getProfileBanner($user_id)
    {
        $banner = User::find($user_id)->profile->banner;
        return !empty($banner) ? '/uploads/users/' . $user_id . '/' . $banner : 'images/embanner-350x172.jpg';
    }

    /**
     * Upload Attachments.
     *
     * @param mixed $path         path     path
     * @param mixed $uploadedFile uploaded uploadedFile
     *
     * @return relation
     */
    public static function uploadSingleTempImage($path, $uploadedFile)
    {
        if (!empty($uploadedFile)) {
            $file_original_name = $uploadedFile->getClientOriginalName();
            $parts = explode('.', $file_original_name);
            $extension = end($parts);
            $extension = $uploadedFile->getClientOriginalExtension();
            if ($extension === "jpg" || $extension === "png") {
                // create directory if not exist.
                if (!file_exists($path)) {
                    File::makeDirectory($path, 0744, true, true);
                }
                // generate small image size
                $image = Image::make($uploadedFile);
                $image->save($path . $file_original_name);

                $json['message'] = trans('lang.img_uploaded');
                $json['type'] = 'success';
                return $json;
            } else {
                $json['message'] = trans('lang.img_jpg_png');
                $json['type'] = 'error';
                return $json;
            }
        } else {
            $json['message'] = trans('lang.image not found');
            $json['type'] = 'error';
            return $json;
        }
    }

    /**
     * Get project image
     *
     * @param string  $image   Image
     * @param integer $user_id UserID
     *
     * @access public
     *
     * @return array
     */
    public static function getProjectImage($image, $user_id)
    {
        return !empty($image) ? '/uploads/users/' . $user_id . '/' . $image : 'images/projects/img-01.jpg';
    }

    /**
     * List category in tree format
     *
     * @param integer $parent_id  Image
     * @param string  $cat_indent UserID
     *
     * @access public
     *
     * @return array
     */
    public static function listTreeCategories($parent_id = 0, $cat_indent = '')
    {
        $parent_cat = Location::select('title', 'id', 'parent')->where('parent', $parent_id)->get()->toArray();
        foreach ($parent_cat as $key => $value) {
            echo '<option value="' . $value['id'] . '">' . $cat_indent . $value['title'] . '</option>';
            self::listTreeCategories($value['id'], $cat_indent . '—');
        }
    }

    /**
     * Get total jobs
     *
     * @param string $status Status
     *
     * @access public
     *
     * @return array
     */
    public static function getTotalJobs($status = '')
    {
        if (Auth::user()) {
            if (!empty($status)) {
                return Auth::user()->jobs->where('status', $status)->count();
            } else {
                return Auth::user()->jobs->count();
            }
        }
    }

    /**
     * Get proposal Balance
     *
     * @param int $user_id User ID
     * @param int $status  Status
     *
     * @return \Illuminate\Http\Response
     */
    public static function getProposalsBalance($user_id, $status)
    {
        $commision = SiteManagement::getMetaValue('commision');
        $admin_commission = !empty($commision) && !empty($commision[0]['commision']) ? $commision[0]['commision'] : 0;
        $balance =  Proposal::select('amount')
            ->where('freelancer_id', $user_id)
            ->where('status', $status)->sum('amount');
        $total_amount = !empty($balance) ? $balance - ($balance / 100) * $admin_commission : 0;
        return $total_amount;
    }

    /**
     * Get proposal
     *
     * @param int $user_id User ID
     * @param int $status  Status
     *
     * @return \Illuminate\Http\Response
     */
    public static function getProposals($user_id, $status)
    {
        return Proposal::select('job_id')->latest()->where('freelancer_id', $user_id)->where('status', $status)->get();
    }

    /**
     * Get public path
     *
     * @return \Illuminate\Http\Response
     */
    public static function publicPath()
    {
        $path = public_path();
        if (isset($_SERVER["SERVER_NAME"]) && $_SERVER["SERVER_NAME"] != 'amentotech.com') {
            $path = getcwd();
        }
        return $path;
    }

    /**
     * Get size
     *
     * @param integer $bytes bytes
     *
     * @return \Illuminate\Http\Response
     */
    public static function bytesToHuman($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Format file name
     *
     * @param string $file_name filename
     *
     * @return \Illuminate\Http\Response
     */
    public static function formateFileName($file_name)
    {
        $file =  strstr($file_name, '-');
        return substr($file, 1);
    }

    /**
     * Currency list
     *
     * @param string $code code
     *
     * @access public
     *
     * @return array
     */
    public static function currencyList($code = "")
    {
        $currency_array = array(
            'USD' => array (
                'numeric_code'  => 840 ,
                'code'          => 'USD' ,
                'name'          => 'United States dollar' ,
                'symbol'        => '$' ,
                'fraction_name' => 'Cent[D]' ,
                'decimals'      => 2 ) ,
            'AUD' => array (
                'numeric_code'  => 36 ,
                'code'          => 'AUD' ,
                'name'          => 'Australian dollar' ,
                'symbol'        => '$' ,
                'fraction_name' => 'Cent' ,
                'decimals'      => 2 ) ,
            'BRL' => array (
                'numeric_code'  => 986 ,
                'code'          => 'BRL' ,
                'name'          => 'Brazilian real' ,
                'symbol'        => 'R$' ,
                'fraction_name' => 'Centavo' ,
                'decimals'      => 2 ) ,
            'CAD' => array (
                'numeric_code'  => 124 ,
                'code'          => 'CAD' ,
                'name'          => 'Canadian dollar' ,
                'symbol'        => '$' ,
                'fraction_name' => 'Cent' ,
                'decimals'      => 2 ) ,
            'CZK' => array (
                'numeric_code'  => 203 ,
                'code'          => 'CZK' ,
                'name'          => 'Czech koruna' ,
                'symbol'        => 'Kc' ,
                'fraction_name' => 'Haléř' ,
                'decimals'      => 2 ) ,
            'DKK' => array (
                'numeric_code'  => 208 ,
                'code'          => 'DKK' ,
                'name'          => 'Danish krone' ,
                'symbol'        => 'kr' ,
                'fraction_name' => 'Øre' ,
                'decimals'      => 2 ) ,
            'EUR' => array (
                'numeric_code'  => 978 ,
                'code'          => 'EUR' ,
                'name'          => 'Euro' ,
                'symbol'        => '€' ,
                'fraction_name' => 'Cent' ,
                'decimals'      => 2 ) ,
            'HKD' => array (
                'numeric_code'  => 344 ,
                'code'          => 'HKD' ,
                'name'          => 'Hong Kong dollar' ,
                'symbol'        => '$' ,
                'fraction_name' => 'Cent' ,
                'decimals'      => 2 ) ,
            'HUF' => array (
                'numeric_code'  => 348 ,
                'code'          => 'HUF' ,
                'name'          => 'Hungarian forint' ,
                'symbol'        => 'Ft' ,
                'fraction_name' => 'Fillér' ,
                'decimals'      => 2 ) ,
            'ILS' => array (
                'numeric_code'  => 376 ,
                'code'          => 'ILS' ,
                'name'          => 'Israeli new sheqel' ,
                'symbol'        => '₪' ,
                'fraction_name' => 'Agora' ,
                'decimals'      => 2 ) ,
            'INR' => array (
                'numeric_code'  => 356 ,
                'code'          => 'INR' ,
                'name'          => 'Indian rupee' ,
                'symbol'        => 'INR' ,
                'fraction_name' => 'Paisa' ,
                'decimals'      => 2 ) ,
            'JPY' => array (
                'numeric_code'  => 392 ,
                'code'          => 'JPY' ,
                'name'          => 'Japanese yen' ,
                'symbol'        => '¥' ,
                'fraction_name' => 'Sen[G]' ,
                'decimals'      => 2 ) ,
            'MYR' => array (
                'numeric_code'  => 458 ,
                'code'          => 'MYR' ,
                'name'          => 'Malaysian ringgit' ,
                'symbol'        => 'RM' ,
                'fraction_name' => 'Sen' ,
                'decimals'      => 2 ) ,
            'MXN' => array (
                'numeric_code'  => 484 ,
                'code'          => 'MXN' ,
                'name'          => 'Mexican peso' ,
                'symbol'        => '$' ,
                'fraction_name' => 'Centavo' ,
                'decimals'      => 2 ) ,
            'NOK' => array (
                'numeric_code'  => 578 ,
                'code'          => 'NOK' ,
                'name'          => 'Norwegian krone' ,
                'symbol'        => 'kr' ,
                'fraction_name' => 'Øre' ,
                'decimals'      => 2 ) ,
            'NZD' => array (
                'numeric_code'  => 554 ,
                'code'          => 'NZD' ,
                'name'          => 'New Zealand dollar' ,
                'symbol'        => '$' ,
                'fraction_name' => 'Cent' ,
                'decimals'      => 2 ) ,
            'PHP' => array (
                'numeric_code'  => 608 ,
                'code'          => 'PHP' ,
                'name'          => 'Philippine peso' ,
                'symbol'        => 'PHP' ,
                'fraction_name' => 'Centavo' ,
                'decimals'      => 2 ) ,
            'PLN' => array (
                'numeric_code'  => 985 ,
                'code'          => 'PLN' ,
                'name'          => 'Polish złoty' ,
                'symbol'        => 'zł' ,
                'fraction_name' => 'Grosz' ,
                'decimals'      => 2 ) ,
            'GBP' => array (
                'numeric_code'  => 826 ,
                'code'          => 'GBP' ,
                'name'          => 'British pound[C]' ,
                'symbol'        => '£' ,
                'fraction_name' => 'Penny' ,
                'decimals'      => 2 ) ,
            'SGD' => array (
                'numeric_code'  => 702 ,
                'code'          => 'SGD' ,
                'name'          => 'Singapore dollar' ,
                'symbol'        => '$' ,
                'fraction_name' => 'Cent' ,
                'decimals'      => 2 ) ,
            'SEK' => array (
                'numeric_code'  => 752 ,
                'code'          => 'SEK' ,
                'name'          => 'Swedish krona' ,
                'symbol'        => 'kr' ,
                'fraction_name' => 'Öre' ,
                'decimals'      => 2 ) ,
            'CHF' => array (
                'numeric_code'  => 756 ,
                'code'          => 'CHF' ,
                'name'          => 'Swiss franc' ,
                'symbol'        => 'Fr' ,
                'fraction_name' => 'Rappen[I]' ,
                'decimals'      => 2 ) ,
            'TWD' => array (
                'numeric_code'  => 901 ,
                'code'          => 'TWD' ,
                'name'          => 'New Taiwan dollar' ,
                'symbol'        => '$' ,
                'fraction_name' => 'Cent' ,
                'decimals'      => 2 ) ,
            'THB' => array (
                'numeric_code'  => 764 ,
                'code'          => 'THB' ,
                'name'          => 'Thai baht' ,
                'symbol'        => '฿' ,
                'fraction_name' => 'Satang' ,
                'decimals'      => 2 ) ,
            'RUB' => array (
                'numeric_code'  => 643 ,
                'code'          => 'RUB' ,
                'name'          => 'Russian ruble' ,
                'symbol'        => 'руб.' ,
                'fraction_name' => 'Kopek' ,
                'decimals'      => 2 ) ,
        );

        if (!empty($code) && array_key_exists($code, $currency_array)) {
            return $currency_array[$code];
        } else {
            return $currency_array;
        }
    }

    /**
     * Display email warning
     *
     * @access public
     *
     * @return array
     */
    public static function displayEmailWarning()
    {
        $output = "";
        if (empty(env('MAIL_USERNAME'))
            && empty(env('MAIL_PASSWORD'))
            && auth()->user()->getRoleNames()->first() === 'admin'
        ) {
            $output .= '<div class="wt-jobalertsholder la-email-warning float-right">';
            $output .= '<ul id="wt-jobalerts">';
            $output .= '<li class="alert alert-danger alert-dismissible fade show">';
            $output .= '<span>';
            $output .= trans('lang.ph_email_warning');
            $output .= '</span>';
            $output .= '<a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="Close"><i class="fa fa-close"></i></a>';
            $output .= '</li>';
            $output .= '</ul>';
            $output .= '</div>';
        }
        echo $output;
    }

    /**
     * Get badge
     *
     * @param integer $user_id UserID
     *
     * @access public
     *
     * @return array
     */
    public static function getUserBadge($user_id)
    {
        if (!empty($user_id)) {
            $user = User::find($user_id);
            if (!empty($user->badge_id)) {
                return Badge::where('id', $user->badge_id)->first();
            } else {
                return '';
            }
        }
    }

    /**
     * Get payment method list
     *
     * @param string $key key
     *
     * @access public
     *
     * @return array
     */
    public static function getPaymentMethodList($key = "")
    {
        $list = array(
            'paypal' => array(
                'title' => trans('lang.payment_methods.paypal'),
                'value' => 'paypal',
            ),
            'stripe' => array(
                'title' => trans('lang.payment_methods.stripe'),
                'value' => 'stripe',
            ),
        );
        if (!empty($key) && array_key_exists($key, $list)) {
            return $list[$key];
        } else {
            return $list;
        }
    }

    /**
     * Get employer jobs
     *
     * @param string $user_id key
     *
     * @access public
     *
     * @return array
     */
    public static function getEmployerJobs($user_id)
    {
        if (!empty($user_id)) {
            $user = User::find($user_id);
            if ($user->getRoleNames()->first() === 'employer') {
                return Job::select('title', 'id')->where('user_id', $user_id)->get()->pluck('title', 'id');
            } else {
                return array();
            }
        } else {
            return trans('lang.no_jobs_found');
        }
    }

    /**
     * Get google map api key
     *
     * @access public
     *
     * @return array
     */
    public static function getGoogleMapApiKey()
    {
        $settings =  SiteManagement::getMetaValue('settings');
        if (!empty($settings) && !empty($settings[0]['gmap_api_key'])) {
            return $settings[0]['gmap_api_key'];
        } else {
            return '';
        }
    }

    /**
     * Update payouts
     *
     * @access public
     *
     * @return array
     */
    public static function updatePayouts()
    {
        $payout_settings = SiteManagement::getMetaValue('commision');
        $min_payount = !empty($payout_settings) && !empty($payout_settings[0]['min_payout']) ? $payout_settings[0]['min_payout'] : '';
        $payment_gateway = !empty($payout_settings) && !empty($payout_settings[0]['payment_method']) ? $payout_settings[0]['payment_method'] : 'paypal';
        $payment_settings = SiteManagement::getMetaValue('commision');
        $currency  = !empty($payment_settings) && !empty($payment_settings[0]['currency']) ? $payment_settings[0]['currency'] : 'USD';
        $query = Proposal::select('freelancer_id', DB::raw('sum(amount) earning'))->where('status', 'completed')
            ->groupBy('freelancer_id')
            ->get();
        if ($query->count() > 0) {
            foreach ($query as $q) {
                if ($q->earning >= $min_payount) {
                    $user = User::find($q->freelancer_id);
                    if (!empty($user->profile->payout_id)) {
                        $total_earning = Self::getProposalsBalance($q->freelancer_id, 'completed');
                        $user_payout = Payout::select('id')->where('user_id', $q->freelancer_id)
                            ->get()->first();
                        if (!empty($user_payout->id)) {
                            $payout = Payout::find($user_payout->id);
                        } else {
                            $payout = new Payout();
                        }
                        $payout->user()->associate($q->freelancer_id);
                        $payout->amount = $total_earning;
                        $payout->payment_method = $payment_gateway;
                        $payout->currency = $currency;
                        if (Schema::hasColumn('payouts', 'email')) {
                            $payout->email = $user->profile->payout_id;
                        } elseif (Schema::hasColumn('payouts', 'paypal_id')) {
                            $payout->paypal_id = $user->profile->payout_id;
                        }
                        $payout->paypal_id = $user->profile->payout_id;
                        $payout->status = 'pending';
                        $payout->save();
                    }
                }
            }
        }
    }

    /**
     * Get images
     *
     * @access public
     *
     * @return string
     */
    public static function getImages($path, $image, $default)
    {
        if (file_exists($path . '/' . $image)) {
            echo '<img src="' . url($path . '/' . $image) . '" alt="' . trans('lang.img') . '">';
        } else {
            echo '<span class="lnr lnr-' . $default . '"></span>';
        }
    }

    /**
     * Get package expiry image
     *
     * @param string $path  path
     * @param string $image image
     *
     * @access public
     *
     * @return string
     */
    public static function getDashExpiryImages($path, $image)
    {
        if (file_exists($path . '/' . $image)) {
            return url($path . '/' . $image);
        } else {
            return '';
        }
    }

    /**
     * Get package expiry image
     *
     * @param int $badge_id badge_id
     *
     * @access public
     *
     * @return string
     */
    public static function getBadgeTitle($badge_id)
    {
        $badge = Badge::find($badge_id);
        if (!empty($badge)) {
            return $badge->title;
        }
    }

    /**
     * Demo site refresh page
     *
     * @param string $message message text
     *
     * @access public
     *
     * @return string
     */
    public static function worketicIsDemoSite($message = '')
    {
        $json = array();
        $message = !empty($message) ? $message : trans('lang.restricted_text');
        if (isset($_SERVER["SERVER_NAME"]) && $_SERVER["SERVER_NAME"] === 'amentotech.com') {
            return $message;
        }
    }

    /**
     * Demo site ajax request
     *
     * @param string $message message text
     *
     * @access public
     *
     * @return string
     */
    public static function worketicIsDemoSiteAjax($message = '')
    {
        $message = !empty($message) ? $message : trans('lang.restricted_text');
        if (isset($_SERVER["SERVER_NAME"]) && $_SERVER["SERVER_NAME"] === 'amentotech.com') {
            return response()->json(['message' => $message]);
        }
    }

    /**
     * Display socials
     *
     * @access public
     *
     * @return array
     */
    public static function getBodyLangClass()
    {
        $settings = SiteManagement::getMetaValue('settings');
        if (!empty($settings) && !empty($settings[0]['body-lang-class'])) {
            return $settings[0]['body-lang-class'];
        } else {
            return '';
        }

    }

    /**
     * Get text direction
     *
     * @access public
     *
     * @return string
     */
    public static function getTextDirection()
    {
        $language = \App::getLocale();
        $lang_array = Arr::except(self::getTranslatedLang(), ['en']);
        $rtl = Arr::pluck($lang_array, 'code');
        $textdir = 'ltr';
        if (in_array($language, $rtl)) {
            $textdir = 'rtl';
        }
        return $textdir;
    }

    /**
     * Get home banner
     *
     * @param string $type type
     * @param string $size size
     *
     * @access public
     *
     * @return string
     */
    public static function getHomeBanner($type, $size='')
    {
        $home_page_settings = !empty(SiteManagement::getMetaValue('home_settings')) ? SiteManagement::getMetaValue('home_settings') : array();
        $banner_settings = !empty($home_page_settings) ? $home_page_settings[0] : array();
        $banner  = !empty($banner_settings) && !empty($banner_settings['home_banner']) ? $banner_settings['home_banner'] : '';
        $banner_inner_image  = !empty($banner_settings) && !empty($banner_settings['home_banner_image']) ? $banner_settings['home_banner_image'] : '';
        $banner_title  = !empty($banner_settings) && !empty($banner_settings['banner_title']) ? $banner_settings['banner_title'] : 'Hire expert freelancers';
        $banner_subtitle  = !empty($banner_settings) && !empty($banner_settings['banner_subtitle']) ? $banner_settings['banner_subtitle'] : 'for any job, Online';
        $banner_description  = !empty($banner_settings) && !empty($banner_settings['banner_description']) ? $banner_settings['banner_description'] : 'Consectetur adipisicing elit sed dotem eiusmod tempor incuntes ut labore etdolore maigna aliqua enim';
        $banner_video_link  = !empty($banner_settings) && !empty($banner_settings['video_link']) ? $banner_settings['video_link'] : 'https://www.youtube.com/watch?v=B-ph2g5o2K4';
        $banner_video_title  = !empty($banner_settings) && !empty($banner_settings['video_title']) ? $banner_settings['video_title'] : 'See For Yourself!';
        $banner_video_desc  = !empty($banner_settings) && !empty($banner_settings['video_desc']) ? $banner_settings['video_desc'] : 'How it works & experience the ultimate joy.';
        if ($type == 'image') {
            if (!empty($banner)) {
                return '/uploads/settings/home/' . $banner;
            } else {
                return 'images/banner-bg.jpg';
            }
        }
        if ($type == 'inner_image') {
            if (!empty($banner_inner_image)) {
                if (!empty($size)) {
                    return '/uploads/settings/home/' . $size . '-' . $banner_inner_image;
                } else {
                    return '/uploads/settings/home/' . $banner_inner_image;
                }
            } else {
                return 'images/banner-img.png';
            }
        }
        if ($type == 'title') {
            return $banner_title;
        }
        if ($type == 'subtitle') {
            return $banner_subtitle;
        }
        if ($type == 'description') {
            return $banner_description;
        }
        if ($type == 'video_url') {
            return $banner_video_link;
        }
        if ($type == 'video_title') {
            return $banner_video_title;
        }
        if ($type == 'video_description') {
            return $banner_video_desc;
        }
    }



    /**
     * Get home banner
     *
     * @param string $type type
     *
     * @access public
     *
     * @return string
     */
    public static function getHomeSection($type)
    {
        $section_settings = !empty(SiteManagement::getMetaValue('section_settings')) ? SiteManagement::getMetaValue('section_settings') : array();
        $show_cat_section = !empty($section_settings) && !empty($section_settings[0]['cat_section_display']) ? $section_settings[0]['cat_section_display'] : true;
        $cat_sec_title = !empty($section_settings[0]['cat_sec_title']) ? $section_settings[0]['cat_sec_title'] : trans('lang.explore_cats');
        $cat_sec_subtitle = !empty($section_settings[0]['cat_sec_subtitle']) ? $section_settings[0]['cat_sec_subtitle'] : trans('lang.professional_by_cats');
        $show_section = !empty($section_settings) && !empty($section_settings[0]['home_section_display']) ? $section_settings[0]['home_section_display'] : true;
        $show_app_section = !empty($section_settings) && !empty($section_settings[0]['app_section_display']) ? $section_settings[0]['app_section_display'] : true;
        $section_bg = !empty($section_settings) && !empty($section_settings[0]['section_bg']) ? $section_settings[0]['section_bg'] : null;
        $company_title = !empty($section_settings) && !empty($section_settings[0]['company_title']) ? $section_settings[0]['company_title'] : null;
        $company_desc = !empty($section_settings) && !empty($section_settings[0]['company_desc']) ? $section_settings[0]['company_desc'] : null;
        $company_url = !empty($section_settings) && !empty($section_settings[0]['company_url']) ? $section_settings[0]['company_url'] : '#';
        $freelancer_title = !empty($section_settings) && !empty($section_settings[0]['freelancer_title']) ? $section_settings[0]['freelancer_title'] : null;
        $freelancer_desc = !empty($section_settings) && !empty($section_settings[0]['freelancer_desc']) ? $section_settings[0]['freelancer_desc'] : null;
        $freelancer_url = !empty($section_settings) && !empty($section_settings[0]['freelancer_url']) ? $section_settings[0]['freelancer_url'] : '#';
        if ($type == 'show_cat_section') {
            return $show_cat_section;
        }
        if ($type == 'cat_sec_title') {
            return $cat_sec_title;
        }
        if ($type == 'cat_sec_subtitle') {
            return $cat_sec_subtitle;
        }
        if ($type == 'show_section') {
            return $show_section;
        }
        if ($type == 'show_app_section') {
            return $show_app_section;
        }
        if ($type == 'background_image') {
            if (!empty($section_bg)) {
                return '/uploads/settings/home/' . $section_bg;
            } else {
                return 'images/banner-bg.jpg';
            }
        }
        if ($type == 'left_title') {
            return $company_title;
        }
        if ($type == 'left_description') {
            return $company_desc;
        }
        if ($type == 'left_url') {
            return $company_url;
        }
        if ($type == 'right_title') {
            return $freelancer_title;
        }
        if ($type == 'right_description') {
            return $freelancer_desc;
        }
        if ($type == 'right_url') {
            return $freelancer_url;
        }
    }

    /**
     * Get App section
     *
     * @param string $type type
     *
     * @access public
     *
     * @return string
     */
    public static function getAppSection($type)
    {
        $section_settings = !empty(SiteManagement::getMetaValue('section_settings')) ? SiteManagement::getMetaValue('section_settings') : array();
        $download_app_img = !empty($section_settings) && !empty($section_settings[0]['download_app_img']) ? $section_settings[0]['download_app_img'] : '';
        $app_title = !empty($section_settings) && !empty($section_settings[0]['app_title']) ? $section_settings[0]['app_title'] : '';
        $app_subtitle = !empty($section_settings) && !empty($section_settings[0]['app_subtitle']) ? $section_settings[0]['app_subtitle'] : '';
        $application_desc = SiteManagement::where('meta_key', 'app_desc')->select('meta_value')->pluck('meta_value')->first();
        $app_desc = !empty($application_desc) ? $application_desc : '';
        $application_android_link = SiteManagement::where('meta_key', 'app_android_link')->select('meta_value')->pluck('meta_value')->first();
        $app_android_link = !empty($application_android_link) ? $application_android_link : '#';
        $application_ios_link = SiteManagement::where('meta_key', 'app_ios_link')->select('meta_value')->pluck('meta_value')->first();
        $app_ios_link = !empty($application_ios_link) ? $application_ios_link : '#';
        if ($type == 'image') {
            if (!empty($download_app_img)) {
                return '/uploads/settings/home/' . $download_app_img;
            } else {
                return 'images/mobile-img.png';
            }
        }
        if ($type == 'title') {
            return $app_title;
        }
        if ($type == 'subtitle') {
            return $app_subtitle;
        }
        if ($type == 'description') {
            return $app_desc;
        }
        if ($type == 'android_url') {
            return $app_android_link;
        }
        if ($type == 'ios_url') {
            return $app_ios_link;
        }
    }

    /**
     * Get dashboard icon list
     *
     * @param string $icon icon
     *
     * @access public
     *
     * @return array
     */
    public static function getIconList($icon="")
    {
        $icons = array(
            'latest_proposal' => array(
                'value' => 'latest_proposal',
                'title' => trans('lang.latest_proposals'),
            ),
            'package_expiry' => array(
                'value' => 'package_expiry',
                'title' => trans('lang.pkg_expiry'),
            ),
            'new_message' => array(
                'value' => 'new_message',
                'title' => trans('lang.new_msgs'),
            ),
            'saved_item' => array(
                'value' => 'saved_item',
                'title' => trans('lang.save_items'),
            ),
            'cancel_project' => array(
                'value' => 'cancel_project',
                'title' => trans('lang.cancelled_projects'),
            ),
            'ongoing_project' => array(
                'value' => 'ongoing_project',
                'title' => trans('lang.ongoing_projects'),
            ),
            'pending_balance' => array(
                'value' => 'pending_balance',
                'title' => trans('lang.pending_bal'),
            ),
            'current_balance' => array(
                'value' => 'current_balance',
                'title' => trans('lang.current_bal'),
            ),
            'saved_job' => array(
                'value' => 'saved_job',
                'title' => trans('lang.saved_jobs'),
            ),
            'followed_company' => array(
                'value' => 'followed_company',
                'title' => trans('lang.followed_companies'),
            ),
            'liked_freelancer' => array(
                'value' => 'liked_freelancer',
                'title' => trans('lang.liked_freelancers'),
            ),
            'cancel_job' => array(
                'value' => 'cancel_job',
                'title' => trans('lang.cancelled_jobs'),
            ),
            'ongoing_job' => array(
                'value' => 'ongoing_job',
                'title' => trans('lang.ongoing_jobs'),
            ),
            'completed_job' => array(
                'value' => 'completed_job',
                'title' => trans('lang.completed_jobs'),
            ),
            'posted_job' => array(
                'value' => 'posted_job',
                'title' => trans('lang.posted_jobs'),
            ),
        );

        if (!empty($icon) && array_key_exists($icon, $icons)) {
            return $icons[$icon];
        } else {
            return $icons;
        }
    }

    /**
     * Get color css file
     *
     * @access public
     *
     * @return array
     */
    public static function setSiteStyling()
    {
        $styling = SiteManagement::getMetaValue('styling');
        dd($styling);
        if (!empty($styling)) {
            ob_start(); ?>
            <style>
                /* Theme Text Color */
                a,
                p a,
                p a:hover,
                a:hover,
                a:focus,
                a:active,
                .wt-navigation > ul > li:hover > a,
                .wt-navigation > ul > li.current-menu-item > a,
                .wt-navarticletab li:hover a,
                .wt-navarticletab li a.active,
                .wt-categoriescontent li a:hover,
                .wt-joinsteps li.wt-active a,
                .wt-effectivecontent li:hover a,
                .wt-articlesingle-content .wt-description .wt-blockquotevone q,
                .wt-filtertag li:hover a,
                .wt-userlisting-breadcrumb li .wt-clicksave,
                .wt-clicksave,
                .wt-qrcodefeat h3 span,
                .wt-comfollowers ul li:hover a span,
                .wt-postarticlemeta .wt-following span,
                .tg-qrcodefeat h3 span,
                .active-category

                { color: <?php echo $styling[0]['primary_color']  ?>; }
            </style>
            <?php return ob_get_clean();
        }
    }


}
