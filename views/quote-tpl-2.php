<?php ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Aloha!</title>

        <style type="text/css">
            * {
                font-family: Verdana, Arial, sans-serif;
            }
            table{
                font-size: x-small;
            }
            tfoot tr td{
                font-weight: bold;
                font-size: x-small;
            }
            .gray {
                background-color: lightgray
            }
            .people {
                margin:20px;
            }
            p.label {
                margin-bottom: 15px;
                font-size: small;
            }
            .content, .content > * {
                margin-bottom: 30px;
                font-size: x-small;
            }
        </style>

    </head>
    <body>

        <table width="100%">
            <tr>
                <td valign="top"><img src="<?php echo $logo_path; ?>" alt="" width="150"/></td>
                <td align="right">
                    <!-- <h3>Shinra Electric power company</h3> -->
                    <pre>
                    <h3>DEVIS : SD-<?php echo date('YmdHis'); ?></h3>
                        Emis le <?php echo date('d/m/Y'); ?>
                    </pre>
                </td>
            </tr>
            
        </table>

        <table width="100%">
            <tr>
                <td class="people gray">
                    <h3>Vendeur</h3>
                    <strong> <?php echo $current_user->user_firstname; ?> <?php echo $current_user->user_lastname; ?></strong><br/>
                    <?php echo $current_user->user_email; ?>
                </td>
                <td class="" width="10"></td>
                <td class="people gray">
                    <h3>Client</h3>
                    <strong><?php echo $quote_first_name; ?> <?php echo $quote_last_name; ?></strong><br/>
                    <?php echo $quote_email; ?>
                </td>
            </tr>

        </table>

        <table width="100%">
            <thead style="background-color: lightgray;">
                <tr>
                    <!-- <th>#</th> -->
                    <th style="padding: 15;">Description</th>
                    <!-- <th>Quantity</th> -->
                    <th>Prix (<?php echo get_woocommerce_currency_symbols(); ?>)</th>
                    <!-- <th>Total (<?php echo $currency_symbol; ?>)</th> -->
                </tr>
            </thead>
            <tbody>
                <tr>
                    <!-- <th scope="row">1</th> -->
                    <td><?php echo $quote_description ?></td>
                    <!-- <td align="right">1</td> -->
                    <td align="right"><?php echo wc_price($quote_price); ?></td>
                    <!-- <td align="right">1400.00</td> -->
                </tr>
            </tbody>

            <tfoot>
                <!-- <tr>
                    <td colspan="3"></td>
                    <td align="right">Subtotal $</td>
                    <td align="right">1635.00</td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td align="right">Tax $</td>
                    <td align="right">0.00</td>
                </tr> -->
                <tr>
                    <td colspan="1"></td>
                    <td align="right">Total (<?php echo $currency_symbol; ?>)</td>
                    <td align="right" class="gray"> <?php echo $currency_symbol; ?><?php echo apply_filters('woocommerce_get_price_html', $quote_price); ?></td>
                </tr>
            </tfoot>
        </table>
    
    </body>
</html>