<?php
/*
 * Type: sub|main
 * Page Title: Shortcodes
 * Menu Title: Shortcodes|Prodii
 * Parent: overview
 * Page Slug: prodii
 * Position: 21
 * Capability: administrator
 * Icon: assets/img/prodii.png
 */
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = prodii_get_http_post_var('type', null);
    $id   = prodii_get_http_post_var('id', null);
}

$entries = array(
    'Companies' => array(
        'type' => 'company',
        'data' => ProdiiCompany::get_list(),
        'desc' => 'Listing your active companies from your Prodii account.'
    ),
    'Teams'     => array(
        'type' => 'team',
        'data' => ProdiiTeam::get_list(),
        'desc' => 'Listing your active teams from your Prodii account.'
    ),
    'Profiles'  => array(
        'type' => 'profile',
        'data' => ProdiiProfile::get_list(),
        'desc' => 'Listing all your active team members from your Prodii account.'
    ),
);

?>
<div class="section">
    <h2>How it works:</h2>
    <p>
        Your Access Token connects your Prodii account with your website enabling a flow of data. <br>
        Copy / paste the shortcode into a post or a page where you want it to be displayed.<br>
        For details, visit <a href="?page=prodii-reference">Reference</a><br>
        <br>
        NB! Remember to inform team members that you are publishing their profile on your website. <br>
        For details, read <a href="https://prodii.com/#privacy-policy" target="_blank">our data policy</a>
    </p>
    <?php foreach ($entries as $heading => $data) : ?>
        <h2><?php echo $heading ?></h2>
        <p>
            <?php echo $data['desc'] ?>
        </p>
        <table class="prodii-overview-list">
            <colgroup>
                <col width="1">
                <col>
                <col width="1">
                <col width="1">
            </colgroup>
            <thead>
            <tr>
                <th scope="col" class="column-id">ID</th>
                <th scope="col" class="column-title">Title</th>
                <th scope="col" class="column-shortcode">Shortcode</th>
                <th scope="col" class="column-flush"><span class="dashicons dashicons-redo"></span> Reload</th>
            </tr>
            </thead>
            <?php if (!empty($data['data'])) : $ids = array(); ?>
                <tbody>

                <?php foreach ($data['data'] as $id => $title) : $ids[] = $id ?>
                    <tr>
                        <td id="id" class="id column-id"><?php echo $id ?></td>
                        <td id="title" class="title column-title"><?php echo $title ?></td>
                        <td id="shortcode" class="shortcode column-shortcode">
                            <input type="text" readonly
                                   value='[prodii type="<?php echo $data['type'] ?>" id="<?php echo $id ?>" template="<?php echo get_option('prodii_default_template') ?>"]'>
                        </td>
                        <td id="flush" class="title column-flush">
                            <form method="post">
                                <input type="hidden" name="ids" value="<?php echo $id ?>">
                                <input type="hidden" name="type" value="<?php echo $data['type'] ?>">
                                <button type="submit" name="prodii-action" value="refresh-object-cache"
                                        class="button button-secondary button-small">Reload Cache
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4">
                        <form method="post" style="text-align:right;">
                            <input type="hidden" name="ids" value="<?php echo implode(',', $ids) ?>">
                            <input type="hidden" name="type" value="<?php echo $data['type'] ?>">
                            <button type="submit" name="prodii-action" value="refresh-object-cache"
                                    class="button button-secondary button-small">
                                Reload All <?php echo $heading ?>
                            </button>
                        </form>
                    </td>
                </tr>
                </tfoot>
            <?php elseif (empty($data['data'])) : ?>
                <tr>
                    <td colspan="4">No <?php echo $heading ?> found for Access Token "<b><?php echo get_option('prodii_access_token') ?></b>"</td>
                </tr>
            <?php endif; ?>
        </table>
    <?php endforeach; ?>
</div>
