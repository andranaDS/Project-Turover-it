<?php

namespace App\Company\Event;

class CompanyFeatureEvents
{
    public const SEARCH_DISPLAY_ARRAY = 'search_display_array';
    public const SEARCH_DISPLAY_LIST = 'search_display_list';
    public const SEARCH_BOOLEAN = 'search_boolean';
    public const SEARCH_QUERY = 'search_query';
    public const SEARCH_JOB = 'search_job';
    public const SEARCH_LOCATION = 'search_location';
    public const SEARCH_FOLDER = 'search_folder';
    public const SEARCH_ORDER = 'search_order';
    public const SEARCH_AVAILABILITY_AND_LANGUAGE = 'search_availability_and_language';
    public const USER_CART = 'user_cart';
    public const USER_FAVORITE = 'user_favorite';
    public const USER_HIDE = 'user_hide';
    public const USER_DOWNLOAD_RESUME = 'user_download_resume';
    public const USER_COMMENT = 'user_comment';
    public const USER_FOLDER = 'user_folder';
    public const USER_JOB_POSTING = 'user_job_posting';
    public const USER_EMAIL_TRANSFER = 'user_email_transfer';
    public const USER_EMAIL_SEND = 'user_email_send';
    public const USER_MULTIPLE_FOLDER = 'user_multiple_folder';
    public const USER_MULTIPLE_EXPORT = 'user_multiple_export';
    public const USER_ALERT = 'user_alert';
    public const JOB_POSTING_FREE_WORK = 'job_posting_free_work';
    public const JOB_POSTING_TURNOVER = 'job_posting_turnover';
    public const JOB_POSTING_PUBLIC = 'job_posting_public';
    public const JOB_POSTING_INTERNAL = 'job_posting_internal';
    public const INTERCONTRACT_SEARCH_BY_COMPANY = 'intercontract_search_by_company';
    public const INTERCONTRACT_PUBLISH = 'intercontract_publish';
    public const INTERCONTRACT_ONLY = 'intercontract_only';
    public const COMPANY_PUBLISH = 'company_publish';
    public const COMPANY_LOG = 'company_log';
    public const EXPORT_JOB_POSTING_PUBLISH = 'export_job_posting_publish';
    public const EXPORT_USER_LOG_AND_DOWNLOAD = 'user_log_and_download';
}
