<h2>API Access Details</h2>
<?php
$API = str_replace('dt_intfc', '', $Media['ID']) . '_' . md5(str_replace('dt_intfc', '', $Media['ID']) . $Config['_APISeed']);


?>

<table class="form-table">
    <tbody>

        <tr>
            <th scope="row">URL Structure</th>
            <td>
                <?php echo get_bloginfo('url').'/ <em><strong>CallName</strong></em> / <em><strong>Key</strong></em> / <em><strong>Method</strong></em> / <em><strong>format</strong></em> / <em><strong>Variables</strong></em> '; ?>
            </td>
        </tr>


        <tr>
            <th scope="row">CallName</th>
            <td>
                <?php

                    

                ?>
            </td>
        </tr>
        
        <tr>
            <th scope="row">API Call URL</th>
            <td>
                <?php echo get_bloginfo('url').'/'.$Media['ID'].'/'.$API.'/{operation[list|fetch|edit|delete]}/api.{format[xml|json]}{?_ItemID='.$Config['_ReturnFields'][0].'}'; ?>
            </td>
        </tr>


        <tr>
            <th scope="row">Sharedsecret</th>
            <td><?php echo $API; ?></td>
        </tr>

    </tbody>
</table>
<?php

if (empty($Config['_UseListViewTemplate'])) {
    echo 'API Key: ' . $API . '<br />';

    echo '<div class="row-actions-hide">';
    echo '<strong>List Records</strong><br />';
?>
    <div class="row-actions-show">
        <a href="<?php echo get_bloginfo('url'); ?>/?APIKey=<?php echo $API; ?>&format=xml" target="_blank">XML</a> |
        <a href="<?php echo get_bloginfo('url'); ?>/?APIKey=<?php echo $API; ?>&format=json" target="_blank">JSON</a>
    </div>
    <strong>Insert Records</strong><br />
    POST URL: <input type="text" style="width: 80%;" value="<?php echo get_bloginfo('url'); ?>/?APIKey=<?php echo $API; ?>&action=insert" />

<?php
    $Fields = array();
    foreach ($Config['_Field'] as $Field => $Types) {
        if (!empty($Types)) {
            $Type = explode('_', $Types);
            if ($Type[0] != 'auto') {
                $Fields[] = $Field;
            }
        }
    }
    echo '<div>Submitted Data: ' . implode(', ', $Fields) . '</div>';
    $Fields = array();
    if (!empty($Config['_ReturnFields'])) {
        foreach ($Config['_ReturnFields'] as $Field) {
            $Fields[] = $Field;
        }
        echo '<div>Returned Fields: ' . implode(', ', $Fields) . '</div>';
    }
    echo '</div>';
} else {


    echo "<div class=\"row-actions\">API Only Supported in non-templated list mode</div>";
}
?>