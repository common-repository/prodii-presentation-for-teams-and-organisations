<?php
/*
 * Type: sub
 * Page Title: Reference
 * Menu Title: Reference
 * Parent: overview
 * Capability: administrator
 */

ob_start();
?>

    <div class="prodii-reference">
        <div class="section" id="toc">
            <h2>Table of contents</h2>
        </div>
        <div class="section">
            <h2>Introduction</h2>
            <p>
                The presentation plugin by Prodii supports custom templating for use with your built-in theme for Wordpress.
            </p>
            <p>
                This reference will guide you through the process of building and registering your template for use with <?php echo Prodii::get_name() ?>
            </p>
        </div>
        <div class="section">
            <h2>Setup</h2>
            <p>
                Before you're able to access data on Prodii.com you need to register your access token with the plugin.
            </p>
            <img src="<?php echo Prodii::get_plugin_url() ?>assets/img/settings.png" style="width: 50%;">
            <dl class="explanation">
                <dt>A - Access Token</dt>
                <dd>Enter your access token here - When entered and saved your name should appear next to the text box</dd>
                <dt>B - Ignore SSL</dt>
                <dd>
                    On rare occasions something may go wrong with the SSL (HTTPS) due to wrong time settings etc.
                    This setting allows you to ignore there errors and proceed regardless.
                    This is <b>not</b> recommended as the transfer may be intercepted by a third party
                </dd>
                <dt>C - Default Template</dt>
                <dd>
                    Select your default template - <?php echo Prodii::get_name() ?> comes bundled with one template and can be extended with further templates (See Creating Templates).
                    If you've created or downloaded additional templates then they should be available for selection.
                </dd>
                <dt>C - Clear Cache</dt>
                <dd><?php echo Prodii::get_name() ?> uses caching of data to reduce load times when data from Prodii.com is being used. Click this button to clear <b>everything</b></dd>
            </dl>
        </div>
        <div class="section">
            <h2>Using Built-in Templates</h2>
            <p>
                Select a built-in template in <a href="?page=prodii-settings">Settings</a> and select a shortcode to copy in <a href="?page=prodii-overview">Overview</a>.
            </p>
            <p>
                Insert the shortcode in the Wordpress WYSIWYG editor on whatever page or post you want it to be shown.
            </p>
            <div class="section">
                <h2>Shortcode</h2>
                <p>
                    The basic shortcode format for <?php echo Prodii::get_name() ?> is simple.
                </p>
                <p>
                    <kbd>[prodii type="company" id="1234" template="prodii-copenhagen"]</kbd>
                </p>
                <dl class="explanation">
                    <dt>type:</dt>
                    <dd>Type to be displayed - Valid values are company, team and profile</dd>
                    <dt>id:</dt>
                    <dd>
                        IDs if the entities to be displayed (separated by commas).
                        By default the <a href="?page=prodii-overview">Overview</a> only generates shortcodes with one ID, though more are supported.
                    </dd>
                    <dt>template:</dt>
                    <dd>The template to be used for the display - You can change this if you have multiple templates in use at once and want to use them case-by-case.</dd>
                </dl>
            </div>
        </div>
        <div class="section">
            <h2>Creating Templates</h2>
            <div class="section">
                <h2>Basics</h2>
                <p>
                    Templates for <?php echo Prodii::get_name() ?> are fairly straight forward to develop. See the reference below for the basic setup and how-to.
                </p>
                <div class="section">
                    <h2>Description Header</h2>
                    <p>
                        All templates must at least contain a template.php file with the accompanying description header:
                    </p>
                    <pre>/*
 * Template Name: Copenhagen
 * Template Slug: prodii-copenhagen
 * Scripts: assets/scripts.js, assets/jquery.ellipsis.min.js
 * Styles: assets/css/font-awesome.min.css, assets/css/style.css
 * Version: 1.0.0
 */</pre>
                    <dl class="explanation">
                        <dt>Template Name:</dt>
                        <dd>Display name of the template for selection in the administrative UI</dd>
                        <dt>Template Slug:</dt>
                        <dd>Internal name of the template. This must be unique and is used to identify the template when used in shortcodes. No whitespace or special characters allowed!</dd>
                        <dt>Scripts:</dt>
                        <dd>Optional. Comma-separated list of script files to load whenever the template is in use (path is relative to the template directory)</dd>
                        <dt>Styles:</dt>
                        <dd>Optional. Comma-separated list of stylesheets to load whenever the template is in use (path is relative to the template directory)</dd>
                        <dt>Version:</dt>
                        <dd>Current version of the template. Please update this if you make any changes to the template to ensure cache refresh on the client side.</dd>
                    </dl>
                </div>
                <div class="section">
                    <h2>Fetching Data</h2>
                    <p>
                        Fetching data for the templates is done via three built in functions.
                    </p>
                    <pre>$data = prodii_template_data();
$type = prodii_template_type();
$args = prodii_template_args();</pre>

                    <dl class="explanation">
                        <dt>prodii_template_data() : Array</dt>
                        <dd>This function returns the data that the shortcode generates. Note: This is always an array, even when only a single entity is returned!</dd>
                        <dt>prodii_template_type() : String</dt>
                        <dd>Returns the template type in use. This is for checks when no type specific template is being used, and is therefore obsolete in type specific template</dd>
                        <dt>prodii_template_args() : Array</dt>
                        <dd>Returns an array of key/value pairs. The data returned is exactly the same as is entered in the shortcode parameters (this can be used to extend the functionality of the
                            shortcode if you need to)
                        </dd>
                    </dl>
                    <p>
                        For full class reference of the returned objects please refer to the Class Definition section
                    </p>
                    <div class="section">
                        <h2>Getting Properties</h2>
                        <p>
                            To get a specific property from any given object the get() method must be used,
                            as all data is contained in private fields.
                        </p>
                        <pre>$company->get('email')</pre>
                        <p>
                            <small>See the object references in the code - You should know where to find this if you're a developer</small>
                        </p>
                    </div>
                    <div class="section">
                        <h2>Privacy</h2>
                        <p>
                            All data has a privacy option, which is set by the owner of the data.
                            By default private values cannot be retrieved unless explicitly stated so by the templating code.
                        </p>
                        <pre>$company->get('email', true)</pre>
                        <p>
                            The 2nd parameter states if the privacy setting should be ignored
                        </p>
                    </div>
                </div>
            </div>
            <div class="section">
                <h2>Registering Templates</h2>
                <p>
                    Registering your template is done via WP Hooks.
                </p>
                <p>
                    Add a hook to "prodii_load_templates" where you call <kbd>register_prodii_template()</kbd>.
                    Pass the path to the base directory of the template to the function, and <?php echo Prodii::get_name() ?> should register the template for use.
                </p>
                <pre>add_action('prodii_load_templates', function () {
    register_prodii_template(__DIR__ . '/prodii-templates/copenhagen/');
});</pre>
                <p>
                    You can verify if the registration was successful by checking the Default Template select box in <a href="?page=prodii-settings">Settings</a>
                </p>
            </div>
            <div class="section">
                <h2>File Structure</h2>
                <p>
                    Templates for <?php echo Prodii::get_name() ?> requires a specific file structure to function as intended. Please follow these guidelines accordingly.
                </p>
                <ul class="file-tree">
                    <li>
                        <span style="font-style: italic;">&lt;template-directory&gt;</span>
                        <ul>
                            <li><span>template.php</span><span class="required">Required</span></li>
                            <li><span>template-company.php</span><span class="optional">Optional</span></li>
                            <li><span>template-team.php</span><span class="optional">Optional</span></li>
                            <li><span>template-profile.php</span><span class="optional">Optional</span></li>
                        </ul>
                    </li>
                </ul>
                <p>
                    template.php is required for all types as this describes the template internally.
                </p>
                <p>
                    The type specific files (template-company, template-team and template-profile) are optional,
                    but will be used instead of the default template.php for their specific types if they exists.
                </p>
                <div class="section">
                    <h2>Type and ID specific templates</h2>
                    <p>
                        Further specification of template is possible by appending the entity's ID to the template filename.
                        This is applicable to all types, and allows for
                    </p>
                    <p>
                        Eg. <b>template-company-123.php</b> is prefered for showing a company with the ID 123. (Find the entity ID in <a href="?page=prodii-overview">Overview</a>
                    </p>
                </div>
                <div class="section">
                    <h2>Additional files</h2>
                    <p>
                        It is possible to include additional files for use with the template, such as stylesheets and/or javascript files using the template description header (see the Description
                        Header
                        section for reference)
                    </p>
                    <p>
                        Directory names can be whatever you want i to, as long as no whitespace is used in the directory names.
                    </p>
                    <ul class="file-tree">
                        <li>
                            <span style="font-style: italic;">&lt;template-directory&gt;</span>
                            <ul>
                                <li>...</li>
                                <li>
                                    <span>assets</span>
                                    <ul>
                                        <li><span>scipts.js</span></li>
                                        <li><span>jquery.ellipsis.min.js</span></li>
                                        <li>
                                            <span>css</span>
                                            <ul>
                                                <li><span>style.css</span></li>
                                                <li><span>font-awesome.min.css</span></li>
                                            </ul>
                                        </li>
                                        <li>
                                            <span>fonts</span>
                                            <ul>
                                                <li><span>FontAwesome.otf</span></li>
                                                <li><span>fontawesome-webfont.eot</span></li>
                                                <li><span>fontawesome-webfont.svg</span></li>
                                                <li><span>fontawesome-webfont.ttf</span></li>
                                                <li><span>fontawesome-webfont.woff</span></li>
                                                <li><span>fontawesome-webfont.woff2</span></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <p>
                        <small>Example is taken from the bundled template Copenhagen</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php

echo ob_get_clean();

?>