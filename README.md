  # netFactor CallerId WordPress Plugin
  
  ## Installation
  
  No configuration is required, simply install the plugin and click activate.
  
  Once the plugin is activated, it begins working to update the `innerHTML` or `value` attribute of all matching HTML elements that are prefixed with any of the CSS selectors listed below.
  
  Please note: It only works on `<span>` and `<input>` HTML elements
  
  
   `.nf_companyId`
   `.nf_companyName`
   `.nf_websiteURL`
   `.nf_hqAddress1`
   `.nf_hqAddress2`
   `.nf_hqCity`
   `.nf_hqStateProvReg`
   `.nf_hqZip`
   `.nf_hqCountry`
   `.nf_ipAddress`
   `.nf_geoCity`
   `.nf_geoStateProvReg`
   `.nf_parentCompany`
   `.nf_parentAddress`
   `.nf_parentCity`
   `.nf_parentStateProvReg`
   `.nf_parentZip`
   `.nf_parentCountry`
   `.nf_phone`
   `.nf_industry`
   `.nf_orgWatch`
   `.nf_revenue`
   `.nf_employees`
   `.nf_sic`
   `.nf_stockExchange`
   `.nf_tickerSymbol`
   `.nf_isp`
   `.nf_netRange`
   `.nf_netRangeBegin`
   `.nf_netRangeEnd`
   `.nf_cidr`
   `.nf_timestamp`
  
  ## How It Works
  
  `netfactor_callerId()`
  
  This function contains all the code used to retrieve data, and then incorporate it into the website via Javascript.
 
  It is triggered by the 'wp_footer' action hook (more info: https://codex.wordpress.org/Function_Reference/wp_footer)
 
  The visitors IP address is used to query the VisitorTrack IP-Based API. Then, using `foreach` we loop through all the values 
  returned and use the "key" to create a CSS selector. 
  
  To create a CSS selector, just use the 'Dimension' with a prefix of `nf_` as shown below:
  
  `.nf_companyId`, `.nf_companyName`, and so on.
   
   ## Usage
 
   Example:
   
    <span class="nf_companyId"></span>
   
   OR
   
    <input class="nf_companyId">
 
   The function will find the elements, and insert the value for companyId as the page is loading using Javascript.
   
   ## Enabled Dimensions
   
   A new variable has been added. It's an array containing the string `companyName`. This is used to enable/disable a field, so that it is not used. 
   
   Currently, companyName is the default value. However, by adding additional fields to the array we can "enable" more fields, for now only `companyName` is active
   
   Example: 'CompanyName', 'stockExchange', and 'geoCity' are enabled in the array shown below.
   
   
    $ENABLED_DIMENSIONS = array('companyName','stockExchange','geoCity');
   
   
   Please note: ***DO NOT*** use `nf_` prefix with values in this array
   
   
   ## Changelog
   
   | Version  | Description |
   | ------------- | ------------- |
   | 0.4  | Carefully revised existing logic, and added control structure to handle API request timeouts. Removed debugging shortcode, as it was just complicating things and was no longer necessary this far into development. Added console.log() for timeout error when it occurs. |
   | 0.3  | Removed jQuery wrapper, and replaced previous jQuery approach with pure vanilla JS. Additionally, I've updated the `wp_head` action hook that uses `netfactor_callerId()` as it's callback function. I've replaced `wp_head` with `wp_footer`, this inserts the Javascript in the footer, after the DOM has loaded up. |
   | 0.2  | Added "Company Name" logic, and improved core functionality. Now, Ipify is used to get the public IP address of site visitors. Also, added a variable to both functions that retricts enabled fields. By adding values to the "Enabled Dimensions" array, you can enable more fields. Currently, all fields are disabled ***EXCEPT*** 'companyName'
   | 0.1  | Initial plugin developed, but missing core functionality. API request working, but missing "Company Name" conditional logic.  |
   
   
 
   
  ## Debugging / Troubleshooting
  
  When the VisitorTrack API takes longer than 1 second (1000 ms) to return an answer, the request times out and an error is then printed in the console. 
  
  