<div x-data="{
    formatDate(date) {
        if (!date || isNaN(date.getTime())) return '...';
        return new Intl.DateTimeFormat('fr-FR').format(date);
    },
    getNextDate(startDate, index, unit) {
        if (!startDate) return new Date();
        let d = new Date(startDate);
        // On commence le calcul à partir de l'index 1 pour la première échéance
        if (unit === 'jour') {
            let count = 0;
            while (count < index) {
                d.setDate(d.getDate() + 1);
                if (d.getDay() !== 0) count++; // Exclure Dimanche
            }
        } else if (unit === 'semaine') {
            d.setDate(d.getDate() + (index * 7));
        } else if (unit === 'mois') {
            d.setMonth(d.getMonth() + index);
        } else if (unit === 'annee') {
            d.setFullYear(d.getFullYear() + index);
        }
        return d;
    }
}" 
class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
    
    <h3 class="text-lg font-bold mb-3 text-gray-700">Échéancier Prévisionnel</h3>
    
    <div class="overflow-x-auto bg-white rounded-md shadow-inner">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 uppercase">N°</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 uppercase">Date Échéance</th>
                    <th class="px-4 py-2 text-right text-xs font-bold text-gray-600 uppercase">Capital</th>
                    <th class="px-4 py-2 text-right text-xs font-bold text-gray-600 uppercase">Intérêt</th>
                    <th class="px-4 py-2 text-right text-xs font-bold text-gray-600 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{-- On s'assure que la durée est un entier et supérieure à 0 --}}
                <template x-for="i in (parseInt($wire.duree) || 0)" :key="i">
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="px-4 py-2 text-sm text-gray-500" x-text="i"></td>
                        <td class="px-4 py-2 text-sm text-gray-900 font-medium" 
                            x-text="formatDate(getNextDate($wire.date_credit, i, $wire.unite_temps))"></td>
                        <td class="px-4 py-2 text-right text-sm text-gray-600" 
                            x-text="(parseFloat($wire.capital || 0) / (parseInt($wire.duree) || 1)).toLocaleString('fr-FR', {minimumFractionDigits: 2})"></td>
                        <td class="px-4 py-2 text-right text-sm text-gray-600" 
                            x-text="(parseFloat($wire.interet || 0) / (parseInt($wire.duree) || 1)).toLocaleString('fr-FR', {minimumFractionDigits: 2})"></td>
                        <td class="px-4 py-2 text-right text-sm font-bold text-blue-700" 
                            x-text="((parseFloat($wire.capital || 0) + parseFloat($wire.interet || 0)) / (parseInt($wire.duree) || 1)).toLocaleString('fr-FR', {minimumFractionDigits: 2})"></td>
                    </tr>
                </template>
            </tbody>
            <tfoot class="bg-gray-800 text-white font-bold">
                <tr>
                    <td colspan="2" class="px-4 py-2 text-sm text-center uppercase">Total Général</td>
                    <td class="px-4 py-2 text-right text-sm" x-text="parseFloat($wire.capital || 0).toLocaleString('fr-FR', {minimumFractionDigits: 2})"></td>
                    <td class="px-4 py-2 text-right text-sm" x-text="parseFloat($wire.interet || 0).toLocaleString('fr-FR', {minimumFractionDigits: 2})"></td>
                    <td class="px-4 py-2 text-right text-sm text-yellow-400" x-text="(parseFloat($wire.capital || 0) + parseFloat($wire.interet || 0)).toLocaleString('fr-FR', {minimumFractionDigits: 2})"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>