<?php
/*
 * Type: sub
 * Page Title: Settings
 * Menu Title: Settings
 * Parent: overview
 * Capability: administrator
 */

$owner = ProdiiBase::get_owner(get_option('prodii_access_token'));
?>
<?php $settings = Prodii::get_settings(); ?>
<form method="post" action="options.php">
    <?php settings_fields('prodii-presentation'); ?>
    <h1>Basic</h1>
    <p>
        Settings connects your Wordpress website with your Prodii account to ensure synchronized content (data update).
    </p>
    <p>
        Be responsible!
    </p>
    <p>
        For more detailed description, please visit “Reference”.
    </p>
    <table class="form-table">
        <tr>
            <td colspan="100">
                <h3><?php echo $settings['prodii_access_token']['label'] ?></h3>
                Copy your key from your ‘Account Settings’ within your login on <a href="https://prodii.com"
                                                                                   target="_blank">https://prodii.com</a>
            </td>
        </tr>
        <tr>
            <td>
                <input placeholder="Access Token" required type="text" id="prodii_access_token"
                       name="prodii_access_token" class="regular-text"
                       value="<?php echo $settings['prodii_access_token']['current'] ?>"/>
                <div>
                    <?php if ($owner->get('name', true)) : ?>
                        <?php echo $owner->get('name', true) ?> (<b><?php echo $owner->get('id', true) ?></b>)
                    <?php else : ?>
                        <span>No owner set or access token is invalid</span>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>
    <table class="form-table">
        <tr>
            <td>
                <h3><?php echo $settings['prodii_default_template']['label'] ?></h3>
                Select design template for your company, profile and team presentations.<br>
                <a href="https://blog.prodii.com/en/help-center/templates/documentation/" target="_blank">Get your own
                    customized / design</a><br>
                <a href="?page=prodii-reference#creating-templates-0">
                    How to make your own templates</a><br>
            </td>
        </tr>
        <tr>
            <td>
                <select required id="prodii_default_template" name="prodii_default_template" class="regular-text">
                    <option style="display: none;"> -- Select Default Template --</option>
                    <?php foreach ($settings['prodii_default_template']['options'] as $slug => $option) : ?>
                        <option <?php echo selected($slug, $settings['prodii_default_template']['current']) ?>
                                value="<?php echo $slug ?>"><?php echo $option->get_title() ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php submit_button('Save Basic Settings'); ?>
</form>
<form method="post">
    <table class="form-table">
        <tr>
            <td>
                <h3>Cache Control</h3>
                <p>
                    The Prodii plugin employs a caching strategy to reduce load times for your website.
                </p>
                <p>
                    This cache can grow over time and may need to be flushed once in a while.
                </p>
                <p>
                    <button class="button button-secondary button-small"
                            onclick="jQuery('#cache-list').slideToggle(500)" type="button">Show Cache Overview
                    </button>
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <div id="cache-list" style="display: none;">
                    <table class="table" style="font-family: monospace">
                        <?php foreach (ProdiiCache::get_cache_size() as $type => $entries) : ?>
                            <tbody>
                            <?php foreach ($entries as $size => $file) : ?>
                                <tr>
                                    <td align="right" style="padding: 0;"><?php echo $size ?></td>
                                    <td style="padding: 0 0 0 5px;"><?php $pi = pathinfo($file);
                                        echo "$type/$pi[basename]" ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        <?php endforeach; ?>
                        <tfoot>
                        <tr>
                            <td align="right" style="padding: 0;">
                                <?php echo ProdiiCache::get_total_size() ?>
                            </td>
                            <td style="padding: 0 0 0 5px;">
                                Total
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                    <button type="submit" name="prodii-action" value="clear-cache"
                            class="button button-primary button-small">Clear
                        Cache
                    </button>
                </div>
            </td>
        </tr>
    </table>
</form>