<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        /* Configuration de la page */
        @page {
            margin: 1.5cm;
        }

        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            font-size: 11px; 
            color: #334155; /* Slate 700 */
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        /* En-tête */
        header { 
            position: relative;
            border-bottom: 2px solid #0284c7; /* Sky 600 */
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #0c4a6e; /* Sky 900 */
            text-transform: uppercase;
            margin: 0;
        }

        .document-type {
            font-size: 14px;
            color: #64748b; /* Slate 500 */
            margin-top: 5px;
        }

        /* Pied de page */
        footer { 
            position: fixed; 
            bottom: -20px; 
            left: 0;
            right: 0;
            font-size: 9px; 
            text-align: center; 
            color: #94a3b8;
            border-top: 0.5px solid #e2e8f0;
            padding-top: 10px;
        }

        /* Contenu et Tableaux */
        .content {
            width: 100%;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }

        th { 
            background-color: #f8fafc; /* Slate 50 */
            color: #475569; /* Slate 600 */
            font-weight: bold; 
            text-transform: uppercase;
            font-size: 9px;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 8px;
            text-align: left;
        }

        td { 
            padding: 10px 8px; 
            border-bottom: 0.5px solid #f1f5f9;
        }

        /* Utilitaires */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-blue { color: #0284c7; }
        
        /* Zebra stripes */
        tr:nth-child(even) {
            background-color: #fcfcfd;
        }
    </style>
</head>
<body>

<header>
    <table style="border: none; margin: 0;">
        <tr>
            <td style="border: none; padding: 0; width: 60%;">
                <div class="company-name">COOPÉRATIVE D’ÉPARGNE</div>
                <div class="document-type">Système de Gestion des Membres</div>
            </td>
            <td style="border: none; padding: 0; width: 40%; text-align: right; vertical-align: top;">
                <div style="font-size: 10px; color: #64748b;">
                    RDC, Grand Bandundu<br>
                    Kikwit, Ville Basse
                </div>
            </td>
        </tr>
    </table>
</header>

<div class="content">
    @yield('content')
</div>

<footer>
    <div>Document confidentiel - COOPÉRATIVE D’ÉPARGNE &copy; {{ date('Y') }}</div>
    <div style="margin-top: 5px;">
        Généré le {{ now()->format('d/m/Y à H:i') }} 
        — Page <span class="pagenum"></span>
    </div>
</footer>

{{-- Script pour la numérotation des pages --}}
<script type="text/php">
    if (isset($pdf)) {
        $text = "{PAGE_NUM} / {PAGE_COUNT}";
        $size = 9;
        $font = $fontMetrics->getFont("DejaVu Sans");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width);
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size);
    }
</script>

</body>
</html>