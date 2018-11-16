  # netFactor CallerId WordPress Plugin
  
  ## Installation
  
  No configuration is required, simply install the plugin and click activate.
  
  Once the plugin is activated, it begins working to update the `innerHTML` property of all matching HTML elements that are prefixed with any of the CSS selectors listed below.
  
  Also, it works on all typographic elements, and form fields!
  
  
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
 
  It is triggered by the 'wp_head' action hook (more info: https://codex.wordpress.org/Function_Reference/wp_head)
 
  This function uses the visitors IP address to GET data from the VisitorTrack IP-Based API. I loop through all the values 
  returned and use the key to create a CSS selector. 
  
  I've used the 'Dimension' with a prefix of `nf_` to create a CSS selector that looks like this:
  
  `.nf_companyId`
  
  `.nf_companyName`
  
  `.nf_websiteUrl`
  
   and so on...
 
   Anywhere these classes are used, the netfactor_callerId() function defined below will target
   all of the CSS selectors in the Dimension column listed on page 5 of the VisitorTrack IP-Based API Documentation
   and using jQuery will insert the respective value into the targeted HTML element.
 
   For example, anywhere on the site where there is an HTML element that looks like this:
   
   `<span class="nf_companyId"></span>`
 
   The function will find the element, and insert the respective value on page load for companyId using jQuery .html() function.
   If the element doesn't exist, nothing happens. If the element does exist, then the appropriate value is inserted into element.
   
   
   Please note: this will empty the node so be sure to use something like span above to avoid any content being erased accidentally.
   
   
   ## Debugging
   
   There is a debug shortcode used to troubleshoot any issues with the autofill functionality:
   
   `[netfactor_debug]`
   
   This shortcode prints two HTML elements( `<span>`,`<input`) for every value the API returns and assigns a class with the `nf_` prefix like:
   
   `<span class="nf_companyId"></span>`
   
   `<input class="nf_companyId">`
   
   This is done for every Dimension listed on page 5 of the VisitorTrack IP-Based API Documentation so we can easily verify that the plugin is 
   targeting the right elements, and inserting the right value on page load.