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
    .align-top {
            vertical-align:top
    }
    .p10 {
        padding:10px;
    }
    .p20 {
        padding:20px;
    }
    .text-left {
        text-align:left;
    }
    .border-gray {
        border-width:1px;
        border-collapse: collapse;
        border-color:#dedede;
    }
    table.details {
        border-left: 0.01em solid #ccc;
        border-right: 0;
        border-top: 0.01em solid #ccc;
        border-bottom: 0;
        border-collapse: collapse;
    }
    table.details td,
    table.details th {
        border-left: 0;
        border-right: 0.01em solid #ccc;
        border-top: 0;
        border-bottom: 0.01em solid #ccc;
    }
    </style>

    </head>
    <body>

    <table width="100%">
        <tr>
            <td valign="top" class="p10"><img src="<?php echo $logo_path; ?>" alt="" width="150"/></td>
            <td align="right" class="align-top p10">
                <h3>DEVIS : SD-<?php echo date('ymdHis'); ?></h3>
                <pre>
                    Emis le <?php echo date('d/m/Y'); ?>
                </pre>
            </td>
        </tr>

    </table>

    <table width="100%">
        <tr height="100">
            <td height="100" width="49%" v-align="top" class="align-top p10 gray">
                <strong>Prestataire:</strong> <br/><br/>
                <?php echo $current_user->user_firstname; ?> <?php echo $current_user->user_lastname; ?><br/>
                <?php echo $current_user->user_email; ?>
            </td>
            <td></td>
            <td width="49%" class="align-top p10 gray">
                <strong>Client:</strong> <br/><br/>
                <?php echo $quote_first_name; ?> <?php echo $quote_last_name; ?><br/>
                <?php echo $quote_email; ?>
            </td>
        </tr>

    </table>

    <br/>

    <table width="100%" class="border-gray details">
        <thead >
        <tr>
            <th class="text-left p10">Description</th>
            <th>Prix <?php echo $currency_symbol; ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="align-top p10"><?php echo $quote_description ?></td>
            <td width="100" align="right" class="p10"><?php echo $quote_price; ?></td>
        </tr>
        </tbody>

        <tfoot>
        
            <tr>
                <td align="right" class="p10">Total </td>
                <td align="right" class="p10"><?php echo $currency_symbol; ?> <?php echo $quote_price; ?></td>
            </tr>
        </tfoot>
    </table>

    </body>
</html>