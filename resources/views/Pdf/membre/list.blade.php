@extends('pdf.layouts.main')

@section('content')
    <div style="margin-bottom: 20px;">
        <h2 style="color: #0c4a6e; margin-bottom: 5px;">Rapport de Synthèse des Membres</h2>
        <p style="color: #64748b; font-size: 10px; margin: 0;">
            Ce document contient la liste exhaustive des membres ainsi que la situation globale des comptes.
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">N° ID</th>
                <th style="width: 25%;">Nom Complet</th>
                <th style="width: 8%;">Sexe</th>
                <th style="width: 15%;">Qualité</th>
                <th style="width: 15%;">Téléphone</th>
                <th class="text-right" style="width: 12%;">Solde USD</th>
                <th class="text-right" style="width: 13%;">Solde CDF</th>
            </tr>
        </thead>
        <tbody>
            @foreach($membres as $membre)
                <tr>
                    <td class="font-bold text-blue">{{ $membre->numero_identification }}</td>
                    <td class="font-bold" style="color: #1e293b;">{{ strtoupper($membre->user->name) }}</td>
                    <td class="text-center">{{ $membre->sexe }}</td>
                    <td>
                        <span style="
                            background-color: #f1f5f9; 
                            color: #475569; 
                            padding: 2px 6px; 
                            border-radius: 4px; 
                            font-size: 9px;
                            border: 0.5px solid #e2e8f0;
                        ">
                            {{ $membre->qualite }}
                        </span>
                    </td>
                    <td style="color: #64748b;">{{ $membre->telephone ?? '---' }}</td>
                    <td class="text-right font-bold" style="color: #0f172a;">
                        {{ number_format($membre->comptes->sum('solde_usd'), 2, '.', ' ') }}
                    </td>
                    <td class="text-right" style="color: #334155;">
                        {{ number_format($membre->comptes->sum('solde_cdf'), 0, ',', ' ') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; border-top: 2px solid #0284c7;">
                <td colspan="5" class="font-bold" style="padding: 15px 8px; font-size: 11px;">TOTAL GÉNÉRAL CUMULÉ</td>
                <td class="text-right font-bold" style="color: #0284c7; font-size: 11px;">
                    {{ number_format($global_usd, 2, '.', ' ') }} $
                </td>
                <td class="text-right font-bold" style="color: #0284c7; font-size: 11px;">
                    {{ number_format($global_cdf, 0, ',', ' ') }} FC
                </td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; width: 300px; float: right; text-align: center;">
        <p style="font-size: 10px; margin-bottom: 40px;">Fait à Kikwit, le {{ now()->format('d/m/Y') }}</p>
        <p style="font-weight: bold; text-decoration: underline;">La Direction Générale</p>
    </div>
@endsection