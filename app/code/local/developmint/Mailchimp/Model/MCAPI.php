<?php
class Developmint_Mailchimp_Model_MCAPI {
    var $version = "1.3";
    var $errorMessage;
    var $errorCode;

    /**
     * Cache the information on the API location on the server
     */
    var $apiUrl;

    /**
     * Default to a 300 second timeout on server calls
     */
    var $timeout = 300;

    /**
     * Default to a 8K chunk size
     */
    var $chunkSize = 8192;

    /**
     * Cache the user api_key so we only have to log in once per client instantiation
     */
    var $api_key;

    /**
     * Use Secure connection or not
     *
     * @var bool
     */
    var $secure = false;

    /**
     * @var array Request params storage
     */
    public $requestParams = array();

    /**
     * Connect to the MailChimp API for a given list.
     *
     * @param string $apikey Your MailChimp apikey
     * @param string $secure Whether or not this should use a secure connection
     */
    function __construct($apikey, $secure=false) {
        $this->secure = $secure;
        $this->apiUrl = parse_url("http://api.mailchimp.com/" . $this->version . "/?output=php");
        $this->api_key = $apikey;
    }
    function setTimeout($seconds){
        if (is_int($seconds)){
            $this->timeout = $seconds;
            return true;
        }
    }
    function getTimeout(){
        return $this->timeout;
    }
    function useSecure($val){
        if ($val===true){
            $this->secure = true;
        } else {
            $this->secure = false;
        }
    }

    /**
     * Unschedule a campaign that is scheduled to be sent in the future
     *
     * @section Campaign  Related
     * @example mcapi_campaignUnschedule.php
     * @example xml-rpc_campaignUnschedule.php
     *
     * @param string $cid the id of the campaign to unschedule
     * @return boolean true on success
     */
    function campaignUnschedule($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignUnschedule", $params);
    }

    /**
     * Schedule a campaign to be sent in the future
     *
     * @section Campaign  Related
     * @example mcapi_campaignSchedule.php
     * @example xml-rpc_campaignSchedule.php
     *
     * @param string $cid the id of the campaign to schedule
     * @param string $schedule_time the time to schedule the campaign. For A/B Split "schedule" campaigns, the time for Group A - in YYYY-MM-DD HH:II:SS format in <strong>GMT</strong>
     * @param string $schedule_time_b optional -the time to schedule Group B of an A/B Split "schedule" campaign - in YYYY-MM-DD HH:II:SS format in <strong>GMT</strong>
     * @return boolean true on success
     */
    function campaignSchedule($cid, $schedule_time, $schedule_time_b=NULL) {
        $params = array();
        $params["cid"] = $cid;
        $params["schedule_time"] = $schedule_time;
        $params["schedule_time_b"] = $schedule_time_b;
        return $this->callServer("campaignSchedule", $params);
    }

    /**
     * Resume sending an AutoResponder or RSS campaign
     *
     * @section Campaign  Related
     *
     * @param string $cid the id of the campaign to pause
     * @return boolean true on success
     */
    function campaignResume($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignResume", $params);
    }

    /**
     * Pause an AutoResponder orRSS campaign from sending
     *
     * @section Campaign  Related
     *
     * @param string $cid the id of the campaign to pause
     * @return boolean true on success
     */
    function campaignPause($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignPause", $params);
    }

    /**
     * Send a given campaign immediately. For RSS campaigns, this will "start" them.
     *
     * @section Campaign  Related
     *
     * @example mcapi_campaignSendNow.php
     * @example xml-rpc_campaignSendNow.php
     *
     * @param string $cid the id of the campaign to send
     * @return boolean true on success
     */
    function campaignSendNow($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignSendNow", $params);
    }

    /**
     * Send a test of this campaign to the provided email address
     *
     * @section Campaign  Related
     *
     * @example mcapi_campaignSendTest.php
     * @example xml-rpc_campaignSendTest.php
     *
     * @param string $cid the id of the campaign to test
     * @param array $test_emails an array of email address to receive the test message
     * @param string $send_type optional by default (null) both formats are sent - "html" or "text" send just that format
     * @return boolean true on success
     */
    function campaignSendTest($cid, $test_emails=array (
    ), $send_type=NULL) {
        $params = array();
        $params["cid"] = $cid;
        $params["test_emails"] = $test_emails;
        $params["send_type"] = $send_type;
        return $this->callServer("campaignSendTest", $params);
    }


    function campaignSegmentTest($list_id, $options) {
        $params = array();
        $params["list_id"] = $list_id;
        $params["options"] = $options;
        return $this->callServer("campaignSegmentTest", $params);
    }


    function campaignCreate($type, $options, $content, $segment_opts=NULL, $type_opts=NULL) {
        $params = array();
        $params["type"] = $type;
        $params["options"] = $options;
        $params["content"] = $content;
        $params["segment_opts"] = $segment_opts;
        $params["type_opts"] = $type_opts;
        return $this->callServer("campaignCreate", $params);
    }

    function campaignUpdate($cid, $name, $value) {
        $params = array();
        $params["cid"] = $cid;
        $params["name"] = $name;
        $params["value"] = $value;
        return $this->callServer("campaignUpdate", $params);
    }


    function campaignReplicate($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignReplicate", $params);
    }


    function campaignDelete($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignDelete", $params);
    }

    function campaigns($filters=array (
    ), $start=0, $limit=25) {
        $params = array();
        $params["filters"] = $filters;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaigns", $params);
    }

    /**
     * Given a list and a campaign, get all the relevant campaign statistics (opens, bounces, clicks, etc.)
     *
     * @section Campaign  Stats
     *
     * @example mcapi_campaignStats.php
     * @example xml-rpc_campaignStats.php
     *
     * @param string $cid the campaign id to pull stats for (can be gathered using campaigns())
     * @return array struct of the statistics for this campaign
     * @returnf int syntax_errors Number of email addresses in campaign that had syntactical errors.
     * @returnf int hard_bounces Number of email addresses in campaign that hard bounced.
     * @returnf int soft_bounces Number of email addresses in campaign that soft bounced.
     * @returnf int unsubscribes Number of email addresses in campaign that unsubscribed.
     * @returnf int abuse_reports Number of email addresses in campaign that reported campaign for abuse.
     * @returnf int forwards Number of times email was forwarded to a friend.
     * @returnf int forwards_opens Number of times a forwarded email was opened.
     * @returnf int opens Number of times the campaign was opened.
     * @returnf date last_open Date of the last time the email was opened.
     * @returnf int unique_opens Number of people who opened the campaign.
     * @returnf int clicks Number of times a link in the campaign was clicked.
     * @returnf int unique_clicks Number of unique recipient/click pairs for the campaign.
     * @returnf date last_click Date of the last time a link in the email was clicked.
     * @returnf int users_who_clicked Number of unique recipients who clicked on a link in the campaign.
     * @returnf int emails_sent Number of email addresses campaign was sent to.
     * @returnf array absplit If this was an absplit campaign, stats for the A and B groups will be returned
    int bounces_a bounces for the A group
    int bounces_b bounces for the B group
    int forwards_a forwards for the A group
    int forwards_b forwards for the B group
    int abuse_reports_a abuse reports for the A group
    int abuse_reports_b abuse reports for the B group
    int unsubs_a unsubs for the A group
    int unsubs_b unsubs for the B group
    int recipients_click_a clicks for the A group
    int recipients_click_b clicks for the B group
    int forwards_opens_a opened forwards for the A group
    int forwards_opens_b opened forwards for the A group
     * @returnf array timewarp If this campaign was a Timewarp campaign, an array of stats from each timezone for it, with the GMT offset as they key. Each timezone will contain:
    int opens opens for this timezone
    string last_open the date/time of the last open for this timezone
    int unique_opens the unique opens for this timezone
    int clicks the total clicks for this timezone
    string last_click the date/time of the last click for this timezone
    int unique_opens the unique clicks for this timezone
    int bounces the total bounces for this timezone
    int total the total number of members sent to in this timezone
    int sent the total number of members delivered to in this timezone
     */
    function campaignStats($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignStats", $params);
    }

    /**
     * Get an array of the urls being tracked, and their click counts for a given campaign
     *
     * @section Campaign  Stats
     *
     * @example mcapi_campaignClickStats.php
     * @example xml-rpc_campaignClickStats.php
     *
     * @param string $cid the campaign id to pull stats for (can be gathered using campaigns())
     * @return struct urls will be keys and contain their associated statistics:
     * @returnf int clicks Number of times the specific link was clicked
     * @returnf int unique Number of unique people who clicked on the specific link
     */
    function campaignClickStats($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignClickStats", $params);
    }

    /**
     * Get the top 5 performing email domains for this campaign. Users want more than 5 should use campaign campaignEmailStatsAIM()
     * or campaignEmailStatsAIMAll() and generate any additional stats they require.
     *
     * @section Campaign  Stats
     *
     * @example mcapi_campaignEmailDomainPerformance.php
     *
     * @param string $cid the campaign id to pull email domain performance for (can be gathered using campaigns())
     * @return array domains email domains and their associated stats
     * @returnf string domain Domain name or special "Other" to roll-up stats past 5 domains
     * @returnf int total_sent Total Email across all domains - this will be the same in every row
     * @returnf int emails Number of emails sent to this domain
     * @returnf int bounces Number of bounces
     * @returnf int opens Number of opens
     * @returnf int clicks Number of clicks
     * @returnf int unsubs Number of unsubs
     * @returnf int delivered Number of deliveries
     * @returnf int emails_pct Percentage of emails that went to this domain (whole number)
     * @returnf int bounces_pct Percentage of bounces from this domain (whole number)
     * @returnf int opens_pct Percentage of opens from this domain (whole number)
     * @returnf int clicks_pct Percentage of clicks from this domain (whole number)
     * @returnf int unsubs_pct Percentage of unsubs from this domain (whole number)
     */
    function campaignEmailDomainPerformance($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignEmailDomainPerformance", $params);
    }

    /**
     * Get all email addresses the campaign was successfully sent to (ie, no bounces)
     *
     * @section Campaign  Stats
     *
     * @param string $cid the campaign id to pull members for (can be gathered using campaigns())
     * @param string $status optional the status to pull - one of 'sent', 'hard' (bounce), or 'soft' (bounce). By default, all records are returned
     * @param int    $start optional for large data sets, the page number to start at - defaults to 1st page of data (page 0)
     * @param int    $limit optional for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
     * @return array a total of all matching emails and the specific emails for this page
     * @returnf int total   the total number of members for the campaign and status
     * @returnf array data  the full campaign member records
    string email the email address sent to
    string status the status of the send - one of 'sent', 'hard', 'soft'
    string absplit_group if this was an absplit campaign, one of 'a','b', or 'winner'
    string tz_group if this was an timewarp campaign the timezone GMT offset the member was included in
     */
    function campaignMembers($cid, $status=NULL, $start=0, $limit=1000) {
        $params = array();
        $params["cid"] = $cid;
        $params["status"] = $status;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaignMembers", $params);
    }

    /**
     * <strong>DEPRECATED</strong> Get all email addresses with Hard Bounces for a given campaign
     *
     * @deprecated See campaignMembers() for a replacement
     *
     * @section Campaign  Stats
     *
     * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
     * @param int    $start optional for large data sets, the page number to start at - defaults to 1st page of data (page 0)
     * @param int    $limit optional for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
     * @return array a total of all hard bounced emails and the specific emails for this page
     * @returnf int total   the total number of hard bounces for the campaign
     * @returnf array data  the full email addresses that bounced
    string email the email address that bounced
     */
    function campaignHardBounces($cid, $start=0, $limit=1000) {
        $params = array();
        $params["cid"] = $cid;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaignHardBounces", $params);
    }

    /**
     * <strong>DEPRECATED</strong> Get all email addresses with Soft Bounces for a given campaign
     *
     * @deprecated See campaignMembers() for a replacement
     *
     * @section Campaign  Stats
     *
     * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
     * @param int    $start optional for large data sets, the page number to start at - defaults to 1st page of data (page 0)
     * @param int    $limit optional for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
     * @return array a total of all soft bounced emails and the specific emails for this page
     * @returnf int total   the total number of soft bounces for the campaign
     * @returnf array data the full email addresses that bounced
    string email the email address that bounced
     */
    function campaignSoftBounces($cid, $start=0, $limit=1000) {
        $params = array();
        $params["cid"] = $cid;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaignSoftBounces", $params);
    }

    /**
     * Get all unsubscribed email addresses for a given campaign
     *
     * @section Campaign  Stats
     *
     * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
     * @param int    $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     * @param int    $limit optional for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
     * @return array email addresses that unsubscribed from this campaign along with reasons, if given
     * @return array a total of all unsubscribed emails and the specific emails for this page
     * @returnf int total   the total number of unsubscribes for the campaign
     * @returnf array data  the full email addresses that unsubscribed
    string email the email address that unsubscribed
    string reason For unsubscribes only - the reason collected for the unsubscribe. If populated, one of 'NORMAL','NOSIGNUP','INAPPROPRIATE','SPAM','OTHER'
    string reason_text For unsubscribes only - if the reason is OTHER, the text entered.
     */
    function campaignUnsubscribes($cid, $start=0, $limit=1000) {
        $params = array();
        $params["cid"] = $cid;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaignUnsubscribes", $params);
    }

    /**
     * Get all email addresses that complained about a given campaign
     *
     * @section Campaign  Stats
     *
     * @example mcapi_campaignAbuseReports.php
     *
     * @param string $cid the campaign id to pull abuse reports for (can be gathered using campaigns())
     * @param int $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     * @param int $limit optional for large data sets, the number of results to return - defaults to 500, upper limit set at 1000
     * @param string $since optional pull only messages since this time - use YYYY-MM-DD HH:II:SS format in <strong>GMT</strong>
     * @return array reports the abuse reports for this campaign
     * @returnf string date date/time the abuse report was received and processed
     * @returnf string email the email address that reported abuse
     * @returnf string type an internal type generally specifying the orginating mail provider - may not be useful outside of filling report views
     */
    function campaignAbuseReports($cid, $since=NULL, $start=0, $limit=500) {
        $params = array();
        $params["cid"] = $cid;
        $params["since"] = $since;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaignAbuseReports", $params);
    }

    /**
     * Retrieve the text presented in our app for how a campaign performed and any advice we may have for you - best
     * suited for display in customized reports pages. Note: some messages will contain HTML - clean tags as necessary
     *
     * @section Campaign  Stats
     *
     * @example mcapi_campaignAdvice.php
     *
     * @param string $cid the campaign id to pull advice text for (can be gathered using campaigns())
     * @return array advice on the campaign's performance
     * @returnf msg the advice message
     * @returnf type the "type" of the message. one of: negative, positive, or neutral
     */
    function campaignAdvice($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignAdvice", $params);
    }

    /**
     * Retrieve the Google Analytics data we've collected for this campaign. Note, requires Google Analytics Add-on to be installed and configured.
     *
     * @section Campaign  Stats
     *
     * @example mcapi_campaignAnalytics.php
     *
     * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
     * @return array analytics we've collected for the passed campaign.
     * @returnf int visits number of visits
     * @returnf int pages number of page views
     * @returnf int new_visits new visits recorded
     * @returnf int bounces vistors who "bounced" from your site
     * @returnf double time_on_site the total time visitors spent on your sites
     * @returnf int goal_conversions number of goals converted
     * @returnf double goal_value value of conversion in dollars
     * @returnf double revenue revenue generated by campaign
     * @returnf int transactions number of transactions tracked
     * @returnf int ecomm_conversions number Ecommerce transactions tracked
     * @returnf array goals an array containing goal names and number of conversions
     */
    function campaignAnalytics($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignAnalytics", $params);
    }

    /**
     * Retrieve the countries and number of opens tracked for each. Email address are not returned.
     *
     * @section Campaign  Stats
     *
     *
     * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
     * @return array countries an array of countries where opens occurred
     * @returnf string code The ISO3166 2 digit country code
     * @returnf string name A version of the country name, if we have it
     * @returnf int opens The total number of opens that occurred in the country
     * @returnf bool region_detail Whether or not a subsequent call to campaignGeoOpensByCountry() will return anything
     */
    function campaignGeoOpens($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignGeoOpens", $params);
    }

    /**
     * Retrieve the regions and number of opens tracked for a certain country. Email address are not returned.
     *
     * @section Campaign  Stats
     *
     *
     * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
     * @param string $code An ISO3166 2 digit country code
     * @return array regions an array of regions within the provided country where opens occurred.
     * @returnf string code An internal code for the region. When this is blank, it indicates we know the country, but not the region
     * @returnf string name The name of the region, if we have one. For blank "code" values, this will be "Rest of Country"
     * @returnf int opens The total number of opens that occurred in the country
     */
    function campaignGeoOpensForCountry($cid, $code) {
        $params = array();
        $params["cid"] = $cid;
        $params["code"] = $code;
        return $this->callServer("campaignGeoOpensForCountry", $params);
    }

    /**
     * Retrieve the tracked eepurl mentions on Twitter
     *
     * @section Campaign  Stats
     *
     *
     * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
     * @return array stats an array containing tweets, retweets, clicks, and referrer related to using the campaign's eepurl
     * @returnf array twitter various Twitter related stats
    int tweets Total number of tweets seen
    string first_tweet date and time of the first tweet seen
    string last_tweet date and time of the last tweet seen
    int retweets Total number of retweets seen
    string first_retweet date and time of the first retweet seen
    string last_retweet date and time of the last retweet seen
    array statuses an array of statuses recorded inclduing the status, screen_name, status_id, and datetime fields plus an is_retweet flag
     * @returnf array clicks stats related to click-throughs on the eepurl
    int clicks Total number of clicks seen
    string first_click date and time of the first click seen
    string last_click date and time of the first click seen
    array locations an array of geographic locations including country, region, and total clicks
     * @returnf array referrers an array of arrays, each containing
    string referrer the referrer, truncated to 100 bytes
    int clicks Total number of clicks seen from this referrer
    string first_click date and time of the first click seen from this referrer
    string last_click date and time of the first click seen from this referrer
     */
    function campaignEepUrlStats($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignEepUrlStats", $params);
    }

    /**
     * Retrieve the most recent full bounce message for a specific email address on the given campaign.
     * Messages over 30 days old are subject to being removed
     *
     *
     * @section Campaign  Stats
     *
     * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
     * @param string $email the email address or unique id of the member to pull a bounce message for.
     * @return array the full bounce message for this email+campaign along with some extra data.
     * @returnf string date date/time the bounce was received and processed
     * @returnf string email the email address that bounced
     * @returnf string message the entire bounce message received
     */
    function campaignBounceMessage($cid, $email) {
        $params = array();
        $params["cid"] = $cid;
        $params["email"] = $email;
        return $this->callServer("campaignBounceMessage", $params);
    }

    /**
     * Retrieve the full bounce messages for the given campaign. Note that this can return very large amounts
     * of data depending on how large the campaign was and how much cruft the bounce provider returned. Also,
     * message over 30 days old are subject to being removed
     *
     * @section Campaign  Stats
     *
     * @example mcapi_campaignBounceMessages.php
     *
     * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
     * @param int $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     * @param int $limit optional for large data sets, the number of results to return - defaults to 25, upper limit set at 50
     * @param string $since optional pull only messages since this time - use YYYY-MM-DD format in <strong>GMT</strong> (we only store the date, not the time)
     * @return array bounces the full bounce messages for this campaign
     * @returnf int total that total number of bounce messages for the campaign
     * @returnf array data an array containing the data for this page
    string date date/time the bounce was received and processed
    string email the email address that bounced
    string message the entire bounce message received
     */
    function campaignBounceMessages($cid, $start=0, $limit=25, $since=NULL) {
        $params = array();
        $params["cid"] = $cid;
        $params["start"] = $start;
        $params["limit"] = $limit;
        $params["since"] = $since;
        return $this->callServer("campaignBounceMessages", $params);
    }

    /**
     * Retrieve the Ecommerce Orders tracked by campaignEcommOrderAdd()
     *
     * @section Campaign  Stats
     *
     * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
     * @param int $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     * @param int $limit optional for large data sets, the number of results to return - defaults to 100, upper limit set at 500
     * @param string $since optional pull only messages since this time - use YYYY-MM-DD HH:II:SS format in <strong>GMT</strong>
     * @return array the total matching orders and the specific orders for the requested page
     * @returnf int total the total matching orders
     * @returnf array data the actual data for each order being returned
    string store_id the store id generated by the plugin used to uniquely identify a store
    string store_name the store name collected by the plugin - often the domain name
    string order_id the internal order id the store tracked this order by
    string email  the email address that received this campaign and is associated with this order
    double order_total the order total
    double tax_total the total tax for the order (if collected)
    double ship_total the shipping total for the order (if collected)
    string order_date the date the order was tracked - from the store if possible, otherwise the GMT time we recieved it
    array lines containing detail of the order - product, category, quantity, item cost
     */
    function campaignEcommOrders($cid, $start=0, $limit=100, $since=NULL) {
        $params = array();
        $params["cid"] = $cid;
        $params["start"] = $start;
        $params["limit"] = $limit;
        $params["since"] = $since;
        return $this->callServer("campaignEcommOrders", $params);
    }

    /**
     * Get the URL to a customized <a href="http://eepurl.com/gKmL" target="_blank">VIP Report</a> for the specified campaign and optionally send an email to someone with links to it. Note subsequent calls will overwrite anything already set for the same campign (eg, the password)
     *
     * @section Campaign  Related
     *
     * @param string $cid the campaign id to share a report for (can be gathered using campaigns())
     * @param array  $opts optional various parameters which can be used to configure the shared report
    string  header_type optional - "text" or "image', defaults to "text'
    string  header_data optional - if "header_type" is text, the text to display. if "header_type" is "image" a valid URL to an image file. Note that images will be resized to be no more than 500x150. Defaults to the Accounts Company Name.
    boolean    secure optional - whether to require a password for the shared report. defaults to "true"
    string  password optional - if secure is true and a password is not included, we will generate one. It is always returned.
    string  to_email optional - optional, email address to share the report with - no value means an email will not be sent
    array   theme  optional - an array containing either 3 or 6 character color code values for: "bg_color", "header_color", "current_tab", "current_tab_text", "normal_tab", "normal_tab_text", "hover_tab", "hover_tab_text"
    string  css_url    optional - a link to an external CSS file to be included after our default CSS (http://vip-reports.net/css/vip.css) <strong>only if</strong> loaded via the "secure_url" - max 255 bytes
     * @return struct Struct containing details for the shared report
     * @returnf string title The Title of the Campaign being shared
     * @returnf string url The URL to the shared report
     * @returnf string secure_url The URL to the shared report, including the password (good for loading in an IFRAME). For non-secure reports, this will not be returned
     * @returnf string password If secured, the password for the report, otherwise this field will not be returned
     */
    function campaignShareReport($cid, $opts=array (
    )) {
        $params = array();
        $params["cid"] = $cid;
        $params["opts"] = $opts;
        return $this->callServer("campaignShareReport", $params);
    }

    /**
     * Get the content (both html and text) for a campaign either as it would appear in the campaign archive or as the raw, original content
     *
     * @section Campaign  Related
     *
     * @param string $cid the campaign id to get content for (can be gathered using campaigns())
     * @param bool   $for_archive optional controls whether we return the Archive version (true) or the Raw version (false), defaults to true
     * @return struct Struct containing all content for the campaign (see Returned Fields for details
     * @returnf string html The HTML content used for the campgain with merge tags intact
     * @returnf string text The Text content used for the campgain with merge tags intact
     */
    function campaignContent($cid, $for_archive=true) {
        $params = array();
        $params["cid"] = $cid;
        $params["for_archive"] = $for_archive;
        return $this->callServer("campaignContent", $params);
    }

    /**
     * Get the HTML template content sections for a campaign. Note that this <strong>will</strong> return very jagged, non-standard results based on the template
     * a campaign is using. You only want to use this if you want to allow editing template sections in your applicaton.
     *
     * @section Campaign  Related
     *
     * @param string $cid the campaign id to get content for (can be gathered using campaigns())
     * @return array array containing all content section for the campaign -
     */
    function campaignTemplateContent($cid) {
        $params = array();
        $params["cid"] = $cid;
        return $this->callServer("campaignTemplateContent", $params);
    }

    /**
     * Retrieve the list of email addresses that opened a given campaign with how many times they opened - note: this AIM function is free and does
     * not actually require the AIM module to be installed
     *
     * @section Campaign Report Data
     *
     * @param string $cid the campaign id to get opens for (can be gathered using campaigns())
     * @param int    $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     * @param int    $limit optional for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
     * @return array array containing the total records matched and the specific records for this page
     * @returnf int total the total number of records matched
     * @returnf array data the actual opens data, including:
    string email Email address that opened the campaign
    int open_count Total number of times the campaign was opened by this email address
     */
    function campaignOpenedAIM($cid, $start=0, $limit=1000) {
        $params = array();
        $params["cid"] = $cid;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaignOpenedAIM", $params);
    }

    /**
     * Retrieve the list of email addresses that did not open a given campaign
     *
     * @section Campaign Report Data
     *
     * @param string $cid the campaign id to get no opens for (can be gathered using campaigns())
     * @param int    $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     * @param int    $limit optional for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
     * @return array array containing the total records matched and the specific records for this page
     * @returnf int total the total number of records matched
     * @returnf array data the email addresses that did not open the campaign
    string email Email address that opened the campaign
     */
    function campaignNotOpenedAIM($cid, $start=0, $limit=1000) {
        $params = array();
        $params["cid"] = $cid;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaignNotOpenedAIM", $params);
    }

    /**
     * Return the list of email addresses that clicked on a given url, and how many times they clicked
     *
     * @section Campaign Report Data
     *
     * @param string $cid the campaign id to get click stats for (can be gathered using campaigns())
     * @param string $url the URL of the link that was clicked on
     * @param int    $start optional for large data sets, the page number to start at - defaults to 1st page of data (page 0)
     * @param int    $limit optional for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
     * @return array array containing the total records matched and the specific records for this page
     * @returnf int total the total number of records matched
     * @returnf array data the email addresses that did not open the campaign
    string email Email address that opened the campaign
    int clicks Total number of times the URL was clicked on by this email address
     */
    function campaignClickDetailAIM($cid, $url, $start=0, $limit=1000) {
        $params = array();
        $params["cid"] = $cid;
        $params["url"] = $url;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaignClickDetailAIM", $params);
    }

    /**
     * Given a campaign and email address, return the entire click and open history with timestamps, ordered by time
     *
     * @section Campaign Report Data
     *
     * @param string $cid the campaign id to get stats for (can be gathered using campaigns())
     * @param array $email_address an array of up to 50 email addresses to check OR the email "id" returned from listMemberInfo, Webhooks, and Campaigns. For backwards compatibility, if a string is passed, it will be treated as an array with a single element (will not work with XML-RPC).
     * @return array an array with the keys listed in Returned Fields below
     * @returnf int success the number of email address records found
     * @returnf int error the number of email address records which could not be found
     * @returnf array data arrays containing the actions (opens and clicks) that the email took, with timestamps
    string action The action taken (open or click)
    string timestamp Time the action occurred
    string url For clicks, the URL that was clicked
     */
    function campaignEmailStatsAIM($cid, $email_address) {
        $params = array();
        $params["cid"] = $cid;
        $params["email_address"] = $email_address;
        return $this->callServer("campaignEmailStatsAIM", $params);
    }

    /**
     * Given a campaign and correct paging limits, return the entire click and open history with timestamps, ordered by time,
     * for every user a campaign was delivered to.
     *
     * @section Campaign Report Data
     * @example mcapi_campaignEmailStatsAIMAll.php
     *
     * @param string $cid the campaign id to get stats for (can be gathered using campaigns())
     * @param int $start optional for large data sets, the page number to start at - defaults to 1st page of data (page 0)
     * @param int $limit optional for large data sets, the number of results to return - defaults to 100, upper limit set at 1000
     * @return array Array containing a total record count and data including the actions  (opens and clicks) for each email, with timestamps
     * @returnf int total the total number of records
     * @returnf array data each record with their details:
    string action The action taken (open or click)
    string timestamp Time the action occurred
    string url For clicks, the URL that was clicked
     */
    function campaignEmailStatsAIMAll($cid, $start=0, $limit=100) {
        $params = array();
        $params["cid"] = $cid;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("campaignEmailStatsAIMAll", $params);
    }

    /**
     * Attach Ecommerce Order Information to a Campaign. This will generall be used by ecommerce package plugins
     * <a href="/plugins/ecomm360.phtml">that we provide</a> or by 3rd part system developers.
     * @section Campaign  Related
     *
     * @param array $order an array of information pertaining to the order that has completed. Use the following keys:
    string id the Order Id
    string campaign_id the Campaign Id to track this order with (see the "mc_cid" query string variable a campaign passes)
    string email_id the Email Id of the subscriber we should attach this order to (see the "mc_eid" query string variable a campaign passes)
    double total The Order Total (ie, the full amount the customer ends up paying)
    string order_date optional the date of the order - if this is not provided, we will default the date to now
    double shipping optional the total paid for Shipping Fees
    double tax optional the total tax paid
    string store_id a unique id for the store sending the order in (20 bytes max)
    string store_name optional a "nice" name for the store - typically the base web address (ie, "store.mailchimp.com"). We will automatically update this if it changes (based on store_id)
    string plugin_id the MailChimp assigned Plugin Id. Get yours by <a href="/register.php">registering here</a>
    array items the individual line items for an order using these keys:
    <div style="padding-left:30px"><table><tr><td colspan=*>
    int line_num optional the line number of the item on the order. We will generate these if they are not passed
    int product_id the store's internal Id for the product. Lines that do no contain this will be skipped
    string product_name the product name for the product_id associated with this item. We will auto update these as they change (based on product_id)
    int category_id the store's internal Id for the (main) category associated with this product. Our testing has found this to be a "best guess" scenario
    string category_name the category name for the category_id this product is in. Our testing has found this to be a "best guess" scenario. Our plugins walk the category heirarchy up and send "Root - SubCat1 - SubCat4", etc.
    double qty the quantity of the item ordered
    double cost the cost of a single item (ie, not the extended cost of the line)
    </td></tr></table></div>
     * @return bool true if the data is saved, otherwise an error is thrown.
     */
    function campaignEcommOrderAdd($order) {
        $params = array();
        $params["order"] = $order;
        return $this->callServer("campaignEcommOrderAdd", $params);
    }

    /**
     * Retrieve all of the lists defined for your user account
     *
     * @section List Related
     * @example mcapi_lists.php
     * @example xml-rpc_lists.php
     *
     * @param array $filters a hash of filters to apply to this query - all are optional:
    string list_id optional - return a single list using a known list_id. Accepts multiples separated by commas when not using exact matching
    string list_name optional - only lists that match this name
    string from_name optional - only lists that have a default from name matching this
    string from_email optional - only lists that have a default from email matching this
    string from_subject optional - only lists that have a default from email matching this
    string created_before optional - only show lists that were created before this date/time (in GMT) - format is YYYY-MM-DD HH:mm:ss (24hr)
    string created_after optional - only show lists that were created since this date/time (in GMT) - format is YYYY-MM-DD HH:mm:ss (24hr)
    boolean exact optional - flag for whether to filter on exact values when filtering, or search within content for filter values - defaults to true
     * @param int $start optional - control paging of lists, start results at this list #, defaults to 1st page of data  (page 0)
     * @param int $limit optional - control paging of lists, number of lists to return with each call, defaults to 25 (max=100)
     * @return array an array with keys listed in Returned Fields below
     * @returnf int total the total number of lists which matched the provided filters
     * @returnf array data the lists which matched the provided filters, including the following for
    string id The list id for this list. This will be used for all other list management functions.
    int web_id The list id used in our web app, allows you to create a link directly to it
    string name The name of the list.
    string date_created The date that this list was created.
    boolean email_type_option Whether or not the List supports multiple formats for emails or just HTML
    boolean use_awesomebar Whether or not campaigns for this list use the Awesome Bar in archives by default
    string default_from_name Default From Name for campaigns using this list
    string default_from_email Default From Email for campaigns using this list
    string default_subject Default Subject Line for campaigns using this list
    string default_language Default Language for this list's forms
    int list_rating An auto-generated activity score for the list (0 - 5)
    array stats various stats and counts for the list
    int member_count The number of active members in the given list.
    int unsubscribe_count The number of members who have unsubscribed from the given list.
    int cleaned_count The number of members cleaned from the given list.
    int member_count_since_send The number of active members in the given list since the last campaign was sent
    int unsubscribe_count_since_send The number of members who have unsubscribed from the given list since the last campaign was sent
    int cleaned_count_since_send The number of members cleaned from the given list since the last campaign was sent
    int campaign_count The number of campaigns in any status that use this list
    int grouping_count The number of Interest Groupings for this list
    int group_count The number of Interest Groups (regardless of grouping) for this list
    int merge_var_count The number of merge vars for this list (not including the required EMAIL one)
    int avg_sub_rate the average number of subscribe per month for the list (empty value if we haven't calculated this yet)
    int avg_unsub_rate the average number of unsubscribe per month for the list (empty value if we haven't calculated this yet)
    int target_sub_rate the target subscription rate for the list to keep it growing (empty value if we haven't calculated this yet)
    int open_rate the average open rate per campaign for the list  (empty value if we haven't calculated this yet)
    int click_rate the average click rate per campaign for the list  (empty value if we haven't calculated this yet)
    array modules Any list specific modules installed for this list (example is SocialPro)
     */
    function lists($filters=array (
    ), $start=0, $limit=25) {
        $params = array();
        $params["filters"] = $filters;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("lists", $params);
    }

    /**
     * Get the list of merge tags for a given list, including their name, tag, and required setting
     *
     * @section List Related
     * @example xml-rpc_listMergeVars.php
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @return array list of merge tags for the list
     * @returnf string name Name of the merge field
     * @returnf bool req Denotes whether the field is required (true) or not (false)
     * @returnf string field_type The "data type" of this merge var. One of: email, text, number, radio, dropdown, date, address, phone, url, imageurl
     * @returnf bool public Whether or not this field is visible to list subscribers
     * @returnf bool show Whether the list owner has this field displayed on their list dashboard
     * @returnf string order The order the list owner has set this field to display in
     * @returnf string default The default value the list owner has set for this field
     * @returnf string size The width of the field to be used
     * @returnf string tag The merge tag that's used for forms and listSubscribe() and listUpdateMember()
     * @returnf array choices For radio and dropdown field types, an array of the options available
     */
    function listMergeVars($id) {
        $params = array();
        $params["id"] = $id;
        return $this->callServer("listMergeVars", $params);
    }

    /**
     * Add a new merge tag to a given list
     *
     * @section List Related
     * @example xml-rpc_listMergeVarAdd.php
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param string $tag The merge tag to add, e.g. FNAME
     * @param string $name The long description of the tag being added, used for user displays
     * @param array $options optional Various options for this merge var. <em>note:</em> for historical purposes this can also take a "boolean"
    string field_type optional one of: text, number, radio, dropdown, date, address, phone, url, imageurl - defaults to text
    boolean req optional indicates whether the field is required - defaults to false
    boolean public optional indicates whether the field is displayed in public - defaults to true
    boolean show optional indicates whether the field is displayed in the app's list member view - defaults to true
    string default_value optional the default value for the field. See listSubscribe() for formatting info. Defaults to blank
    array choices optional kind of - an array of strings to use as the choices for radio and dropdown type fields

     * @return bool true if the request succeeds, otherwise an error will be thrown
     */
    function listMergeVarAdd($id, $tag, $name, $options=array (
    )) {
        $params = array();
        $params["id"] = $id;
        $params["tag"] = $tag;
        $params["name"] = $name;
        $params["options"] = $options;
        return $this->callServer("listMergeVarAdd", $params);
    }

    /**
     * Update most parameters for a merge tag on a given list. You cannot currently change the merge type
     *
     * @section List Related
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param string $tag The merge tag to update
     * @param array $options The options to change for a merge var. See listMergeVarAdd() for valid options
     * @return bool true if the request succeeds, otherwise an error will be thrown
     */
    function listMergeVarUpdate($id, $tag, $options) {
        $params = array();
        $params["id"] = $id;
        $params["tag"] = $tag;
        $params["options"] = $options;
        return $this->callServer("listMergeVarUpdate", $params);
    }

    /**
     * Delete a merge tag from a given list and all its members. Seriously - the data is removed from all members as well!
     * Note that on large lists this method may seem a bit slower than calls you typically make.
     *
     * @section List Related
     * @example xml-rpc_listMergeVarDel.php
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param string $tag The merge tag to delete
     * @return bool true if the request succeeds, otherwise an error will be thrown
     */
    function listMergeVarDel($id, $tag) {
        $params = array();
        $params["id"] = $id;
        $params["tag"] = $tag;
        return $this->callServer("listMergeVarDel", $params);
    }

    /**
     * Get the list of interest groupings for a given list, including the label, form information, and included groups for each
     *
     * @section List Related
     * @example xml-rpc_listInterestGroupings.php
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @return struct list of interest groups for the list
     * @returnf string id The id for the Grouping
     * @returnf string name Name for the Interest groups
     * @returnf string form_field Gives the type of interest group: checkbox,radio,select
     * @returnf array groups Array of the grouping options including the "bit" value, "name", "display_order", and number of "subscribers" with the option selected.
     */
    function listInterestGroupings($id) {
        $params = array();
        $params["id"] = $id;
        return $this->callServer("listInterestGroupings", $params);
    }

    /** Add a single Interest Group - if interest groups for the List are not yet enabled, adding the first
     *  group will automatically turn them on.
     *
     * @section List Related
     * @example xml-rpc_listInterestGroupAdd.php
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param string $group_name the interest group to add - group names must be unique within a grouping
     * @param int optional $grouping_id The grouping to add the new group to - get using listInterestGrouping() . If not supplied, the first grouping on the list is used.
     * @return bool true if the request succeeds, otherwise an error will be thrown
     */
    function listInterestGroupAdd($id, $group_name, $grouping_id=NULL) {
        $params = array();
        $params["id"] = $id;
        $params["group_name"] = $group_name;
        $params["grouping_id"] = $grouping_id;
        return $this->callServer("listInterestGroupAdd", $params);
    }

    /** Delete a single Interest Group - if the last group for a list is deleted, this will also turn groups for the list off.
     *
     * @section List Related
     * @example xml-rpc_listInterestGroupDel.php
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param string $group_name the interest group to delete
     * @param int $grouping_id The grouping to delete the group from - get using listInterestGrouping() . If not supplied, the first grouping on the list is used.
     * @return bool true if the request succeeds, otherwise an error will be thrown
     */
    function listInterestGroupDel($id, $group_name, $grouping_id=NULL) {
        $params = array();
        $params["id"] = $id;
        $params["group_name"] = $group_name;
        $params["grouping_id"] = $grouping_id;
        return $this->callServer("listInterestGroupDel", $params);
    }

    /** Change the name of an Interest Group
     *
     * @section List Related
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param string $old_name the interest group name to be changed
     * @param string $new_name the new interest group name to be set
     * @param int optional $grouping_id The grouping to delete the group from - get using listInterestGrouping() . If not supplied, the first grouping on the list is used.
     * @return bool true if the request succeeds, otherwise an error will be thrown
     */
    function listInterestGroupUpdate($id, $old_name, $new_name, $grouping_id=NULL) {
        $params = array();
        $params["id"] = $id;
        $params["old_name"] = $old_name;
        $params["new_name"] = $new_name;
        $params["grouping_id"] = $grouping_id;
        return $this->callServer("listInterestGroupUpdate", $params);
    }

    /** Add a new Interest Grouping - if interest groups for the List are not yet enabled, adding the first
     *  grouping will automatically turn them on.
     *
     * @section List Related
     * @example xml-rpc_listInterestGroupingAdd.php
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param string $name the interest grouping to add - grouping names must be unique
     * @param string $type The type of the grouping to add - one of "checkboxes", "hidden", "dropdown", "radio"
     * @param array $groups The lists of initial group names to be added - at least 1 is required and the names must be unique within a grouping. If the number takes you over the 60 group limit, an error will be thrown.
     * @return int the new grouping id if the request succeeds, otherwise an error will be thrown
     */
    function listInterestGroupingAdd($id, $name, $type, $groups) {
        $params = array();
        $params["id"] = $id;
        $params["name"] = $name;
        $params["type"] = $type;
        $params["groups"] = $groups;
        return $this->callServer("listInterestGroupingAdd", $params);
    }

    /** Update an existing Interest Grouping
     *
     * @section List Related
     * @example xml-rpc_listInterestGroupingUpdate.php
     *
     * @param int $grouping_id the interest grouping id - get from listInterestGroupings()
     * @param string $name The name of the field to update - either "name" or "type". Groups with in the grouping should be manipulated using the standard listInterestGroup* methods
     * @param string $value The new value of the field. Grouping names must be unique - only "hidden" and "checkboxes" grouping types can be converted between each other.
     * @return bool true if the request succeeds, otherwise an error will be thrown
     */
    function listInterestGroupingUpdate($grouping_id, $name, $value) {
        $params = array();
        $params["grouping_id"] = $grouping_id;
        $params["name"] = $name;
        $params["value"] = $value;
        return $this->callServer("listInterestGroupingUpdate", $params);
    }

    /** Delete an existing Interest Grouping - this will permanently delete all contained interest groups and will remove those selections from all list members
     *
     * @section List Related
     * @example xml-rpc_listInterestGroupingDel.php
     *
     * @param int $grouping_id the interest grouping id - get from listInterestGroupings()
     * @return bool true if the request succeeds, otherwise an error will be thrown
     */
    function listInterestGroupingDel($grouping_id) {
        $params = array();
        $params["grouping_id"] = $grouping_id;
        return $this->callServer("listInterestGroupingDel", $params);
    }

    /** Return the Webhooks configured for the given list
     *
     * @section List Related
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @return array list of webhooks
     * @returnf string url the URL for this Webhook
     * @returnf array actions the possible actions and whether they are enabled
     * @returnf array sources the possible sources and whether they are enabled
     */
    function listWebhooks($id) {
        $params = array();
        $params["id"] = $id;
        return $this->callServer("listWebhooks", $params);
    }

    /** Add a new Webhook URL for the given list
     *
     * @section List Related
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param string $url a valid URL for the Webhook - it will be validated. note that a url may only exist on a list once.
     * @param array $actions optional a hash of actions to fire this Webhook for
    boolean subscribe optional as subscribes occur, defaults to true
    boolean unsubscribe optional as subscribes occur, defaults to true
    boolean profile optional as profile updates occur, defaults to true
    boolean cleaned optional as emails are cleaned from the list, defaults to true
    boolean upemail optional when  subscribers change their email address, defaults to true
     * @param array $sources optional a hash of sources to fire this Webhook for
    boolean user optional user/subscriber initiated actions, defaults to true
    boolean admin optional admin actions in our web app, defaults to true
    boolean api optional actions that happen via API calls, defaults to false
     * @return bool true if the call succeeds, otherwise an exception will be thrown
     */
    function listWebhookAdd($id, $url, $actions=array (
    ), $sources=array (
    )) {
        $params = array();
        $params["id"] = $id;
        $params["url"] = $url;
        $params["actions"] = $actions;
        $params["sources"] = $sources;
        return $this->callServer("listWebhookAdd", $params);
    }

    /** Delete an existing Webhook URL from a given list
     *
     * @section List Related
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param string $url the URL of a Webhook on this list
     * @return boolean true if the call succeeds, otherwise an exception will be thrown
     */
    function listWebhookDel($id, $url) {
        $params = array();
        $params["id"] = $id;
        $params["url"] = $url;
        return $this->callServer("listWebhookDel", $params);
    }

    /** Retrieve all of the Static Segments for a list.
     *
     * @section List Related
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @return array an array of parameters for each static segment
     * @returnf int id the id of the segment
     * @returnf string name the name for the segment
     * @returnf int member_count the total number of members currently in a segment
     * @returnf date created_date the date/time the segment was created
     * @returnf date last_update the date/time the segment was last updated (add or del)
     * @returnf date last_reset the date/time the segment was last reset (ie had all members cleared from it)
     */
    function listStaticSegments($id) {
        $params = array();
        $params["id"] = $id;
        return $this->callServer("listStaticSegments", $params);
    }

    /** Save a segment against a list for later use. There is no limit to the number of segments which can be saved. Static Segments <strong>are not</strong> tied
     *  to any merge data, interest groups, etc. They essentially allow you to configure an unlimited number of custom segments which will have standard performance.
     *  When using proper segments, Static Segments are one of the available options for segmentation just as if you used a merge var (and they can be used with other segmentation
     *  options), though performance may degrade at that point.
     *
     * @section List Related
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param string $name a unique name per list for the segment - 50 byte maximum length, anything longer will throw an error
     * @return int the id of the new segment, otherwise an error will be thrown.
     */
    function listStaticSegmentAdd($id, $name) {
        $params = array();
        $params["id"] = $id;
        $params["name"] = $name;
        return $this->callServer("listStaticSegmentAdd", $params);
    }

    /** Resets a static segment - removes <strong>all</strong> members from the static segment. Note: does not actually affect list member data
     *
     * @section List Related
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param int $seg_id the id of the static segment to reset  - get from listStaticSegments()
     * @return bool true if it worked, otherwise an error is thrown.
     */
    function listStaticSegmentReset($id, $seg_id) {
        $params = array();
        $params["id"] = $id;
        $params["seg_id"] = $seg_id;
        return $this->callServer("listStaticSegmentReset", $params);
    }

    /** Delete a static segment. Note that this will, of course, remove any member affiliations with the segment
     *
     * @section List Related
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param int $seg_id the id of the static segment to delete - get from listStaticSegments()
     * @return bool true if it worked, otherwise an error is thrown.
     */
    function listStaticSegmentDel($id, $seg_id) {
        $params = array();
        $params["id"] = $id;
        $params["seg_id"] = $seg_id;
        return $this->callServer("listStaticSegmentDel", $params);
    }

    /** Add list members to a static segment. It is suggested that you limit batch size to no more than 10,000 addresses per call. Email addresses must exist on the list
     *  in order to be included - this <strong>will not</strong> subscribe them to the list!
     *
     * @section List Related
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param int $seg_id the id of the static segment to modify - get from listStaticSegments()
     * @param array $batch an array of email addresses and/or unique_ids to add to the segment
     * @return array an array with the results of the operation
     * @returnf int success the total number of successful updates (will include members already in the segment)
     * @returnf array errors the email address, an error code, and a message explaining why they couldn't be added
     */
    function listStaticSegmentMembersAdd($id, $seg_id, $batch) {
        $params = array();
        $params["id"] = $id;
        $params["seg_id"] = $seg_id;
        $params["batch"] = $batch;
        return $this->callServer("listStaticSegmentMembersAdd", $params);
    }

    /** Remove list members from a static segment. It is suggested that you limit batch size to no more than 10,000 addresses per call. Email addresses must exist on the list
     *  in order to be removed - this <strong>will not</strong> unsubscribe them from the list!
     *
     * @section List Related
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param int $seg_id the id of the static segment to delete - get from listStaticSegments()
     * @param array $batch an array of email addresses and/or unique_ids to remove from the segment
     * @return array an array with the results of the operation
     * @returnf int success the total number of succesful removals
     * @returnf array errors the email address, an error code, and a message explaining why they couldn't be removed
     */
    function listStaticSegmentMembersDel($id, $seg_id, $batch) {
        $params = array();
        $params["id"] = $id;
        $params["seg_id"] = $seg_id;
        $params["batch"] = $batch;
        return $this->callServer("listStaticSegmentMembersDel", $params);
    }

    /**
     * Subscribe the provided email to a list. By default this sends a confirmation email - you will not see new members until the link contained in it is clicked!
     *
     * @section List Related
     *
     * @example mcapi_listSubscribe.php
     * @example json_listSubscribe.php
     * @example xml-rpc_listSubscribe.php
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param string $email_address the email address to subscribe
     * @param array $merge_vars optional merges for the email (FNAME, LNAME, etc.) (see examples below for handling "blank" arrays). Note that a merge field can only hold up to 255 bytes. Also, there are a few "special" keys:
    string EMAIL set this to change the email address. This is only respected on calls using update_existing or when passed to listUpdateMember()
    array GROUPINGS Set Interest Groups by Grouping. Each element in this array should be an array containing the "groups" parameter which contains a comma delimited list of Interest Groups to add. Commas in Interest Group names should be escaped with a backslash. ie, "," =&gt; "\," and either an "id" or "name" parameter to specify the Grouping - get from listInterestGroupings()
    string OPTINIP Set the Opt-in IP fields. <em>Abusing this may cause your account to be suspended.</em> We do validate this and it must not be a private IP address.
    array MC_LOCATION Set the members geographic location. By default if this merge field exists, we'll update using the optin_ip if it exists. If the array contains LATITUDE and LONGITUDE keys, they will be used. NOTE - this will slow down each subscribe call a bit, especially for lat/lng pairs in sparsely populated areas. Currently our automated background processes can and will overwrite this based on opens and clicks.

    <strong>Handling Field Data Types</strong> - most fields you can just pass a string and all is well. For some, though, that is not the case...
    Field values should be formatted as follows:
    string address For the string version of an Address, the fields should be delimited by <strong>2</strong> spaces. Address 2 can be skipped. The Country should be a 2 character ISO-3166-1 code and will default to your default country if not set
    array address For the array version of an Address, the requirements for Address 2 and Country are the same as with the string version. Then simply pass us an array with the keys <strong>addr1</strong>, <strong>addr2</strong>, <strong>city</strong>, <strong>state</strong>, <strong>zip</strong>, <strong>country</strong> and appropriate values for each

    string date use YYYY-MM-DD to be safe. Generally, though, anything strtotime() understands we'll understand - <a href="http://us2.php.net/strtotime" target="_blank">http://us2.php.net/strtotime</a>
    string dropdown can be a normal string - we <em>will</em> validate that the value is a valid option
    string image must be a valid, existing url. we <em>will</em> check its existence
    string multi_choice can be a normal string - we <em>will</em> validate that the value is a valid option
    double number pass in a valid number - anything else will turn in to zero (0). Note, this will be rounded to 2 decimal places
    string phone If your account has the US Phone numbers option set, this <em>must</em> be in the form of NPA-NXX-LINE (404-555-1212). If not, we assume an International number and will simply set the field with what ever number is passed in.
    string website This is a standard string, but we <em>will</em> verify that it looks like a valid URL

     * @param string $email_type optional email type preference for the email (html, text, or mobile defaults to html)
     * @param bool $double_optin optional flag to control whether a double opt-in confirmation message is sent, defaults to true. <em>Abusing this may cause your account to be suspended.</em>
     * @param bool $update_existing optional flag to control whether a existing subscribers should be updated instead of throwing and error, defaults to false
     * @param bool $replace_interests optional flag to determine whether we replace the interest groups with the groups provided, or we add the provided groups to the member's interest groups (optional, defaults to true)
     * @param bool $send_welcome optional if your double_optin is false and this is true, we will send your lists Welcome Email if this subscribe succeeds - this will *not* fire if we end up updating an existing subscriber. If double_optin is true, this has no effect. defaults to false.
     * @return boolean true on success, false on failure. When using MCAPI.class.php, the value can be tested and error messages pulled from the MCAPI object (see below)
     */
    function listSubscribe($id, $email_address, $merge_vars=NULL, $email_type='html', $double_optin=true, $update_existing=false, $replace_interests=true, $send_welcome=false) {
        $params = array();
        $params["id"] = $id;
        $params["email_address"] = $email_address;
        $params["merge_vars"] = $merge_vars;
        $params["email_type"] = $email_type;
        $params["double_optin"] = $double_optin;
        $params["update_existing"] = $update_existing;
        $params["replace_interests"] = $replace_interests;
        $params["send_welcome"] = $send_welcome;
        return $this->callServer("listSubscribe", $params);
    }

    /**
     * Unsubscribe the given email address from the list
     *
     * @section List Related
     * @example mcapi_listUnsubscribe.php
     * @example xml-rpc_listUnsubscribe.php
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param string $email_address the email address to unsubscribe  OR the email "id" returned from listMemberInfo, Webhooks, and Campaigns
     * @param boolean $delete_member flag to completely delete the member from your list instead of just unsubscribing, default to false
     * @param boolean $send_goodbye flag to send the goodbye email to the email address, defaults to true
     * @param boolean $send_notify flag to send the unsubscribe notification email to the address defined in the list email notification settings, defaults to true
     * @return boolean true on success, false on failure. When using MCAPI.class.php, the value can be tested and error messages pulled from the MCAPI object (see below)
     */
    function listUnsubscribe($id, $email_address, $delete_member=false, $send_goodbye=true, $send_notify=true) {
        $params = array();
        $params["id"] = $id;
        $params["email_address"] = $email_address;
        $params["delete_member"] = $delete_member;
        $params["send_goodbye"] = $send_goodbye;
        $params["send_notify"] = $send_notify;
        return $this->callServer("listUnsubscribe", $params);
    }

    /**
     * Edit the email address, merge fields, and interest groups for a list member. If you are doing a batch update on lots of users,
     * consider using listBatchSubscribe() with the update_existing and possible replace_interests parameter.
     *
     * @section List Related
     * @example mcapi_listUpdateMember.php
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param string $email_address the current email address of the member to update OR the "id" for the member returned from listMemberInfo, Webhooks, and Campaigns
     * @param array $merge_vars array of new field values to update the member with.  See merge_vars in listSubscribe() for details.
     * @param string $email_type change the email type preference for the member ("html", "text", or "mobile").  Leave blank to keep the existing preference (optional)
     * @param boolean $replace_interests flag to determine whether we replace the interest groups with the updated groups provided, or we add the provided groups to the member's interest groups (optional, defaults to true)
     * @return boolean true on success, false on failure. When using MCAPI.class.php, the value can be tested and error messages pulled from the MCAPI object
     */
    function listUpdateMember($id, $email_address, $merge_vars, $email_type='', $replace_interests=true) {
        $params = array();
        $params["id"] = $id;
        $params["email_address"] = $email_address;
        $params["merge_vars"] = $merge_vars;
        $params["email_type"] = $email_type;
        $params["replace_interests"] = $replace_interests;
        return $this->callServer("listUpdateMember", $params);
    }

    /**
     * Subscribe a batch of email addresses to a list at once. If you are using a serialized version of the API, we strongly suggest that you
     * only run this method as a POST request, and <em>not</em> a GET request. Maximum batch sizes vary based on the amount of data in each record,
     * though you should cap them at 5k - 10k records, depending on your experience. These calls are also long, so be sure you increase your timeout values.
     *
     * @section List Related
     *
     * @example mcapi_listBatchSubscribe.php
     * @example xml-rpc_listBatchSubscribe.php
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param array $batch an array of structs for each address to import with two special keys: "EMAIL" for the email address, and "EMAIL_TYPE" for the email type option (html, text, or mobile)
     * @param boolean $double_optin flag to control whether to send an opt-in confirmation email - defaults to true
     * @param boolean $update_existing flag to control whether to update members that are already subscribed to the list or to return an error, defaults to false (return error)
     * @param boolean $replace_interests flag to determine whether we replace the interest groups with the updated groups provided, or we add the provided groups to the member's interest groups (optional, defaults to true)
     * @return struct Array of result counts and any errors that occurred
     * @returnf int add_count Number of email addresses that were succesfully added
     * @returnf int update_count Number of email addresses that were succesfully updated
     * @returnf int error_count Number of email addresses that failed during addition/updating
     * @returnf array errors Array of error arrays, each containing:
    string code the error code
    string message the full error message
    string email the email address being processed
     */
    function listBatchSubscribe($id, $batch, $double_optin=true, $update_existing=false, $replace_interests=true) {
        $params = array();
        $params["id"] = $id;
        $params["batch"] = $batch;
        $params["double_optin"] = $double_optin;
        $params["update_existing"] = $update_existing;
        $params["replace_interests"] = $replace_interests;
        return $this->callServer("listBatchSubscribe", $params);
    }

    /**
     * Unsubscribe a batch of email addresses to a list
     *
     * @section List Related
     * @example mcapi_listBatchUnsubscribe.php
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param array $emails array of email addresses to unsubscribe
     * @param boolean $delete_member flag to completely delete the member from your list instead of just unsubscribing, default to false
     * @param boolean $send_goodbye flag to send the goodbye email to the email addresses, defaults to true
     * @param boolean $send_notify flag to send the unsubscribe notification email to the address defined in the list email notification settings, defaults to false
     * @return struct Array of result counts and any errors that occurred
     * @returnf int success_count Number of email addresses that were succesfully added/updated
     * @returnf int error_count Number of email addresses that failed during addition/updating
     * @returnf array errors Array of error structs. Each error struct will contain "code", "message", and "email"
     */
    function listBatchUnsubscribe($id, $emails, $delete_member=false, $send_goodbye=true, $send_notify=false) {
        $params = array();
        $params["id"] = $id;
        $params["emails"] = $emails;
        $params["delete_member"] = $delete_member;
        $params["send_goodbye"] = $send_goodbye;
        $params["send_notify"] = $send_notify;
        return $this->callServer("listBatchUnsubscribe", $params);
    }

    /**
     * Get all of the list members for a list that are of a particular status. Are you trying to get a dump including lots of merge
     * data or specific members of a list? If so, checkout the <a href="/api/export">Export API</a>
     *
     * @section List Related
     * @example mcapi_listMembers.php
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param string $status the status to get members for - one of(subscribed, unsubscribed, <a target="_blank" href="http://eepurl.com/dwk1">cleaned</a>, updated), defaults to subscribed
     * @param string $since optional pull all members whose status (subscribed/unsubscribed/cleaned) has changed or whose profile (updated) has changed since this date/time (in GMT) - format is YYYY-MM-DD HH:mm:ss (24hr)
     * @param int $start optional for large data sets, the page number to start at - defaults to 1st page of data (page 0)
     * @param int $limit optional for large data sets, the number of results to return - defaults to 100, upper limit set at 15000
     * @return array Array of a the total records match and matching list member data for this page (see Returned Fields for details)
     * @returnf int total the total matching records
     * @returnf array data the data for each member, including:
    string email Member email address
    date timestamp timestamp of their associated status date (subscribed, unsubscribed, cleaned, or updated) in GMT
    string reason For unsubscribes only - the reason collected for the unsubscribe. If populated, one of 'NORMAL','NOSIGNUP','INAPPROPRIATE','SPAM','OTHER'
    string reason_text For unsubscribes only - if the reason is OTHER, the text entered.
     */
    function listMembers($id, $status='subscribed', $since=NULL, $start=0, $limit=100) {
        $params = array();
        $params["id"] = $id;
        $params["status"] = $status;
        $params["since"] = $since;
        $params["start"] = $start;
        $params["limit"] = $limit;
        return $this->callServer("listMembers", $params);
    }

    /**
     * Get all the information for particular members of a list
     *
     * @section List Related
     * @example mcapi_listMemberInfo.php
     * @example xml-rpc_listMemberInfo.php
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param array $email_address an array of up to 50 email addresses to get information for OR the "id"(s) for the member returned from listMembers, Webhooks, and Campaigns. For backwards compatibility, if a string is passed, it will be treated as an array with a single element (will not work with XML-RPC).
     * @return array array of list members with their info in an array (see Returned Fields for details)
     * @returnf int success the number of subscribers successfully found on the list
     * @returnf int errors the number of subscribers who were not found on the list
     * @returnf array data an array of arrays where each one has member info:
    string id The unique id for this email address on an account
    string email The email address associated with this record
    string email_type The type of emails this customer asked to get: html, text, or mobile
    array merges An associative array of all the merge tags and the data for those tags for this email address. <em>Note</em>: Interest Groups are returned as comma delimited strings - if a group name contains a comma, it will be escaped with a backslash. ie, "," =&gt; "\,". Groupings will be returned with their "id" and "name" as well as a "groups" field formatted just like Interest Groups
    string status The subscription status for this email address, either pending, subscribed, unsubscribed, or cleaned
    string ip_opt IP Address this address opted in from.
    string ip_signup IP Address this address signed up from.
    int member_rating the rating of the subscriber. This will be 1 - 5 as described <a href="http://eepurl.com/f-2P" target="_blank">here</a>
    string campaign_id If the user is unsubscribed and they unsubscribed from a specific campaign, that campaign_id will be listed, otherwise this is not returned.
    array lists An associative array of the other lists this member belongs to - the key is the list id and the value is their status in that list.
    date timestamp The time this email address was added to the list
    date info_changed The last time this record was changed. If the record is old enough, this may be blank.
    int web_id The Member id used in our web app, allows you to create a link directly to it
    array clients the various clients we've tracked the address as using - each included array includes client 'name' and 'icon_url'
    array static_segments the 'id', 'name', and date 'added' for any static segment this member is in
     */
    function listMemberInfo($id, $email_address) {
        $params = array();
        $params["id"] = $id;
        $params["email_address"] = $email_address;
        return $this->callServer("listMemberInfo", $params);
    }

    /**
     * Get the most recent 100 activities for particular list members (open, click, bounce, unsub, abuse, sent to)
     *
     * @section List Related
     * @example mcapi_listMemberInfo.php
     * @example xml-rpc_listMemberInfo.php
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @param array $email_address an array of up to 50 email addresses to get information for OR the "id"(s) for the member returned from listMembers, Webhooks, and Campaigns.
     * @return array array of data and success/error counts
     * @returnf int success the number of subscribers successfully found on the list
     * @returnf int errors the number of subscribers who were not found on the list
     * @returnf array data an array of arrays where each activity record has:
    string action The action name, one of: open, click, bounce, unsub, abuse, sent
    string timestamp The date/time of the action
    string url For click actions, the url clicked, otherwise this is empty
    string bounce_type For bounce actions, the bounce type, otherwise this is empty
    string campaign_id The campaign id the action was related to, if it exists - otherwise empty (ie, direct unsub from list)
     */
    function listMemberActivity($id, $email_address) {
        $params = array();
        $params["id"] = $id;
        $params["email_address"] = $email_address;
        return $this->callServer("listMemberActivity", $params);
    }

    /**
     * Get all email addresses that complained about a given campaign
     *
     * @section List Related
     *
     * @example mcapi_listAbuseReports.php
     *
     * @param string $id the list id to pull abuse reports for (can be gathered using lists())
     * @param int $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     * @param int $limit optional for large data sets, the number of results to return - defaults to 500, upper limit set at 1000
     * @param string $since optional pull only messages since this time - use YYYY-MM-DD HH:II:SS format in <strong>GMT</strong>
     * @return array the total of all reports and the specific reports reports this page
     * @returnf int total the total number of matching abuse reports
     * @returnf array data the actual data for each reports, including:
    string date date/time the abuse report was received and processed
    string email the email address that reported abuse
    string campaign_id the unique id for the campaign that report was made against
    string type an internal type generally specifying the orginating mail provider - may not be useful outside of filling report views
     */
    function listAbuseReports($id, $start=0, $limit=500, $since=NULL) {
        $params = array();
        $params["id"] = $id;
        $params["start"] = $start;
        $params["limit"] = $limit;
        $params["since"] = $since;
        return $this->callServer("listAbuseReports", $params);
    }

    /**
     * Access the Growth History by Month for a given list.
     *
     * @section List Related
     *
     * @example mcapi_listGrowthHistory.php
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @return array array of months and growth
     * @returnf string month The Year and Month in question using YYYY-MM format
     * @returnf int existing number of existing subscribers to start the month
     * @returnf int imports number of subscribers imported during the month
     * @returnf int optins number of subscribers who opted-in during the month
     */
    function listGrowthHistory($id) {
        $params = array();
        $params["id"] = $id;
        return $this->callServer("listGrowthHistory", $params);
    }

    /**
     * Access up to the previous 180 days of daily detailed aggregated activity stats for a given list
     *
     * @section List Related
     *
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @return array array of array of daily values, each containing:
     * @returnf string day The day in YYYY-MM-DD
     * @returnf int emails_sent number of emails sent to the list
     * @returnf int unique_opens number of unique opens for the list
     * @returnf int recipient_clicks number of clicks for the list
     * @returnf int hard_bounce number of hard bounces for the list
     * @returnf int soft_bounce number of soft bounces for the list
     * @returnf int abuse_reports number of abuse reports for the list
     * @returnf int subs number of double optin subscribes for the list
     * @returnf int unsubs number of manual unsubscribes for the list
     * @returnf int other_adds number of non-double optin subscribes for the list (manual, API, or import)
     * @returnf int other_removes number of non-manual unsubscribes for the list (deletions, empties, soft-bounce removals)
     */
    function listActivity($id) {
        $params = array();
        $params["id"] = $id;
        return $this->callServer("listActivity", $params);
    }

    /**
     * Retrieve the locations (countries) that the list's subscribers have been tagged to based on geocoding their IP address
     *
     * @section List Related
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @return array array of locations
     * @returnf string country the country name
     * @returnf string cc the 2 digit country code
     * @returnf double percent the percent of subscribers in the country
     * @returnf double total the total number of subscribers in the country
     */
    function listLocations($id) {
        $params = array();
        $params["id"] = $id;
        return $this->callServer("listLocations", $params);
    }

    /**
     * Retrieve the clients that the list's subscribers have been tagged as being used based on user agents seen. Made possible by <a href="http://user-agent-string.info" target="_blank">user-agent-string.info</a>
     *
     * @section List Related
     *
     * @param string $id the list id to connect to. Get by calling lists()
     * @return array the desktop and mobile user agents in use on the list
     * @returnf array desktop desktop user agents and percentages
    double penetration the percent of desktop clients in use
    array clients a record containing the 'client', an 'icon' image url, the 'percent' using the client, and the total 'members' represented
     * @returnf array mobile mobile user agents and percentages
    double penetration the percent of mobile clients in use
    array clients a record containing the 'client', an 'icon' image url, the 'percent' using the client, and the total 'members' represented
     */
    function listClients($id) {
        $params = array();
        $params["id"] = $id;
        return $this->callServer("listClients", $params);
    }

    /**
     * Retrieve various templates available in the system, allowing some thing similar to our template gallery to be created.
     *
     * @section Template  Related
     * @example mcapi_templates.php
     * @example xml-rpc_templates.php
     *
     * @param array $types optional the types of templates to return
    boolean user    Customer template for this user account. Defaults to true.
    boolean gallery Templates from our Gallery. Note that some templates that require extra configuration are withheld. (eg, the Etsy template). Defaults to false.
    boolean base    Our "start from scratch" extremely basic templates
     * @param string $category optional for Gallery templates only, limit to a specific template category
     * @param array $inactives optional options to control how inactive templates are returned, if at all
    boolean include user templates are not deleted, only set inactive. defaults to false.
    boolean only    only include inactive templates. defaults to false.
     * @return array An array of structs, one for each template (see Returned Fields for details)
     * @returnf int id Id of the template
     * @returnf string name Name of the template
     * @returnf string layout Layout of the template - "basic", "left_column", "right_column", or "postcard"
     * @returnf string preview_image If we've generated it, the url of the preview image for the template. We do out best to keep these up to date, but Preview image urls are not guaranteed to be available
     * @returnf string date_created The date/time the template was created
     * @returnf bool edit_source Whether or not you are able to edit the source of a template.
     */
    function templates($types=array (
    ), $category=NULL, $inactives=array (
    )) {
        $params = array();
        $params["types"] = $types;
        $params["category"] = $category;
        $params["inactives"] = $inactives;
        return $this->callServer("templates", $params);
    }

    /**
     * Pull details for a specific template to help support editing
     *
     * @section Template  Related
     *
     * @param int $tid the template id - get from templates()
     * @param string $type the template type to load - one of 'user', 'gallery', 'base'
     * @return array an array of info to be used when editing
     * @returnf array default_content the default content broken down into the named editable sections for the template
     * @returnf array sections the valid editable section names
     * @returnf string source the full source of the template as if you exported it via our template editor
     * @returnf string preview similar to the source, but the rendered version of the source from our popup preview
     */
    function templateInfo($tid, $type='user') {
        $params = array();
        $params["tid"] = $tid;
        $params["type"] = $type;
        return $this->callServer("templateInfo", $params);
    }

    /**
     * Create a new user template, <strong>NOT</strong> campaign content. These templates can then be applied while creating campaigns.
     *
     * @section Template  Related
     * @example mcapi_create_template.php
     * @example xml-rpc_create_template.php
     *
     * @param string $name the name for the template - names must be unique and a max of 50 bytes
     * @param string $html a string specifying the entire template to be created. This is <strong>NOT</strong> campaign content. They are intended to utilize our <a href="http://www.mailchimp.com/resources/email-template-language/" target="_blank">template language</a>.
     * @return int the new template id, otherwise an error is thrown.
     */
    function templateAdd($name, $html) {
        $params = array();
        $params["name"] = $name;
        $params["html"] = $html;
        return $this->callServer("templateAdd", $params);
    }

    /**
     * Replace the content of a user template, <strong>NOT</strong> campaign content.
     *
     * @section Template  Related
     *
     * @param int $id the id of the user template to update
     * @param array  $values the values to updates - while both are optional, at least one should be provided. Both can be updated at the same time.
    string name optional the name for the template - names must be unique and a max of 50 bytes
    string html optional a string specifying the entire template to be created. This is <strong>NOT</strong> campaign content. They are intended to utilize our <a href="http://www.mailchimp.com/resources/email-template-language/" target="_blank">template language</a>.

     * @return boolean true if the template was updated, otherwise an error will be thrown
     */
    function templateUpdate($id, $values) {
        $params = array();
        $params["id"] = $id;
        $params["values"] = $values;
        return $this->callServer("templateUpdate", $params);
    }

    /**
     * Delete (deactivate) a user template
     *
     * @section Template  Related
     *
     * @param int $id the id of the user template to delete
     * @return boolean true if the template was deleted, otherwise an error will be thrown
     */
    function templateDel($id) {
        $params = array();
        $params["id"] = $id;
        return $this->callServer("templateDel", $params);
    }

    /**
     * Undelete (reactivate) a user template
     *
     * @section Template  Related
     *
     * @param int $id the id of the user template to reactivate
     * @return boolean true if the template was deleted, otherwise an error will be thrown
     */
    function templateUndel($id) {
        $params = array();
        $params["id"] = $id;
        return $this->callServer("templateUndel", $params);
    }

    function getAccountDetails() {
        $params = array();
        return $this->callServer("getAccountDetails", $params);
    }

    function generateText($type, $content) {
        $params = array();
        $params["type"] = $type;
        $params["content"] = $content;
        return $this->callServer("generateText", $params);
    }

    function inlineCss($html, $strip_css=false) {
        $params = array();
        $params["html"] = $html;
        $params["strip_css"] = $strip_css;
        return $this->callServer("inlineCss", $params);
    }

    function folders($type='campaign') {
        $params = array();
        $params["type"] = $type;
        return $this->callServer("folders", $params);
    }

    function folderAdd($name, $type='campaign') {
        $params = array();
        $params["name"] = $name;
        $params["type"] = $type;
        return $this->callServer("folderAdd", $params);
    }

    function folderUpdate($fid, $name, $type='campaign') {
        $params = array();
        $params["fid"] = $fid;
        $params["name"] = $name;
        $params["type"] = $type;
        return $this->callServer("folderUpdate", $params);
    }

    function folderDel($fid, $type='campaign') {
        $params = array();
        $params["fid"] = $fid;
        $params["type"] = $type;
        return $this->callServer("folderDel", $params);
    }


    function ecommOrders($start=0, $limit=100, $since=NULL) {
        $params = array();
        $params["start"] = $start;
        $params["limit"] = $limit;
        $params["since"] = $since;
        return $this->callServer("ecommOrders", $params);
    }


    function ecommOrderAdd($order) {
        $params = array();
        $params["order"] = $order;
        return $this->callServer("ecommOrderAdd", $params);
    }

    function ecommOrderDel($store_id, $order_id) {
        $params = array();
        $params["store_id"] = $store_id;
        $params["order_id"] = $order_id;
        return $this->callServer("ecommOrderDel", $params);
    }

    /**
     * Retrieve all List Ids a member is subscribed to.
     *
     * @section Helper
     *
     * @param string $email_address the email address to check OR the email "id" returned from listMemberInfo, Webhooks, and Campaigns
     * @return array An array of list_ids the member is subscribed to.
     */
    function listsForEmail($email_address) {
        $params = array();
        $params["email_address"] = $email_address;
        return $this->callServer("listsForEmail", $params);
    }

    /**
     * Retrieve all Campaigns Ids a member was sent
     *
     * @section Helper
     *
     * @param string $email_address the email address to unsubscribe  OR the email "id" returned from listMemberInfo, Webhooks, and Campaigns
     * @return array An array of campaign_ids the member received
     */
    function campaignsForEmail($email_address) {
        $params = array();
        $params["email_address"] = $email_address;
        return $this->callServer("campaignsForEmail", $params);
    }

    /**
     * Return the current Chimp Chatter messages for an account.
     *
     * @section Helper
     *
     * @return array An array of chatter messages and properties
     * @returnf string message The chatter message
     * @returnf string type The type of the message - one of lists:new-subscriber, lists:unsubscribes, lists:profile-updates, campaigns:facebook-likes, campaigns:facebook-comments, campaigns:forward-to-friend, lists:imports, or campaigns:inbox-inspections
     * @returnf string url a url into the web app that the message could link to
     * @returnf string list_id the list_id a message relates to, if applicable
     * @returnf string campaign_id the list_id a message relates to, if applicable
     * @returnf string update_time The date/time the message was last updated
     */
    function chimpChatter() {
        $params = array();
        return $this->callServer("chimpChatter", $params);
    }

    /**
     * Retrieve a list of all MailChimp API Keys for this User
     *
     * @section Security Related
     * @example xml-rpc_apikeyAdd.php
     * @example mcapi_apikeyAdd.php
     *
     * @param string $username Your MailChimp user name
     * @param string $password Your MailChimp password
     * @param boolean $expired optional - whether or not to include expired keys, defaults to false
     * @return array an array of API keys including:
     * @returnf string apikey The api key that can be used
     * @returnf string created_at The date the key was created
     * @returnf string expired_at The date the key was expired
     */
    function apikeys($username, $password, $expired=false) {
        $params = array();
        $params["username"] = $username;
        $params["password"] = $password;
        $params["expired"] = $expired;
        return $this->callServer("apikeys", $params);
    }

    /**
     * Add an API Key to your account. We will generate a new key for you and return it.
     *
     * @section Security Related
     * @example xml-rpc_apikeyAdd.php
     *
     * @param string $username Your MailChimp user name
     * @param string $password Your MailChimp password
     * @return string a new API Key that can be immediately used.
     */
    function apikeyAdd($username, $password) {
        $params = array();
        $params["username"] = $username;
        $params["password"] = $password;
        return $this->callServer("apikeyAdd", $params);
    }

    /**
     * Expire a Specific API Key. Note that if you expire all of your keys, just visit <a href="http://admin.mailchimp.com/account/api" target="_blank">your API dashboard</a>
     * to create a new one. If you are trying to shut off access to your account for an old developer, change your
     * MailChimp password, then expire all of the keys they had access to. Note that this takes effect immediately, so make
     * sure you replace the keys in any working application before expiring them! Consider yourself warned...
     *
     * @section Security Related
     * @example mcapi_apikeyExpire.php
     * @example xml-rpc_apikeyExpire.php
     *
     * @param string $username Your MailChimp user name
     * @param string $password Your MailChimp password
     * @return boolean true if it worked, otherwise an error is thrown.
     */
    function apikeyExpire($username, $password) {
        $params = array();
        $params["username"] = $username;
        $params["password"] = $password;
        return $this->callServer("apikeyExpire", $params);
    }


    function ping() {
        $params = array();
        return $this->callServer("ping", $params);
    }

    /**
     * Internal function - proxy method for certain XML-RPC calls | DO NOT CALL
     * @param mixed Method to call, with any parameters to pass along
     * @return mixed the result of the call
     */
    function callMethod() {
        $params = array();
        return $this->callServer("callMethod", $params);
    }

    /**
     * Actually connect to the server and call the requested methods, parsing the result
     * You should never have to call this function manually
     */
    function callServer($method, $params) {
        $this->requestParams = $params;
        $dc = "us1";
        if (strstr($this->api_key,"-")){
            list($key, $dc) = explode("-",$this->api_key,2);
            if (!$dc) $dc = "us1";
        }
        $host = $dc.".".$this->apiUrl["host"];
        $params["apikey"] = $this->api_key;

        $this->errorMessage = "";
        $this->errorCode = "";
        $sep_changed = false;
        //sigh, apparently some distribs change this to &amp; by default
        if (ini_get("arg_separator.output")!="&"){
            $sep_changed = true;
            $orig_sep = ini_get("arg_separator.output");
            ini_set("arg_separator.output", "&");
        }
        $post_vars = http_build_query($params);
        if ($sep_changed){
            ini_set("arg_separator.output", $orig_sep);
        }

        $payload = "POST " . $this->apiUrl["path"] . "?" . $this->apiUrl["query"] . "&method=" . $method . " HTTP/1.0\r\n";
        $payload .= "Host: " . $host . "\r\n";
        $payload .= "User-Agent: MCAPI/" . $this->version ."\r\n";
        //$payload .= "User-Agent: " . Mage::helper('mailchimp')->getUserAgent() ."\r\n";
        $payload .= "Content-type: application/x-www-form-urlencoded\r\n";
        $payload .= "Content-length: " . strlen($post_vars) . "\r\n";
        $payload .= "Connection: close \r\n\r\n";
        $payload .= $post_vars;

        ob_start();
        if ($this->secure){
            $sock = fsockopen("ssl://".$host, 443, $errno, $errstr, 30);
        } else {
            $sock = fsockopen($host, 80, $errno, $errstr, 30);
        }
        if(!$sock) {
            $this->errorMessage = "Could not connect (ERR $errno: $errstr)";
            $this->errorCode = "-99";
            ob_end_clean();
            return false;
        }

        $response = "";
        fwrite($sock, $payload);
        stream_set_timeout($sock, $this->timeout);
        $info = stream_get_meta_data($sock);
        while ((!feof($sock)) && (!$info["timed_out"])) {
            $response .= fread($sock, $this->chunkSize);
            $info = stream_get_meta_data($sock);
        }
        fclose($sock);
        ob_end_clean();
        if ($info["timed_out"]) {
            $this->errorMessage = "Could not read response (timed out)";
            $this->errorCode = -98;
            return false;
        }

        list($headers, $response) = explode("\r\n\r\n", $response, 2);
        $headers = explode("\r\n", $headers);
        $errored = false;
        foreach($headers as $h){
            if (substr($h,0,26)==="X-MailChimp-API-Error-Code"){
                $errored = true;
                $error_code = trim(substr($h,27));
                break;
            }
        }

        if(ini_get("magic_quotes_runtime")) $response = stripslashes($response);

        $serial = unserialize($response);
        if($response && $serial === false) {
            $response = array("error" => "Bad Response.  Got This: " . $response, "code" => "-99");
        } else {
            $response = $serial;
        }
        if($errored && is_array($response) && isset($response["error"])) {
            $this->errorMessage = $response["error"];
            $this->errorCode = $response["code"];
            return false;
        } elseif($errored){
            $this->errorMessage = "No error message was found";
            $this->errorCode = $error_code;
            return false;
        }
        return $response;
    }
}
