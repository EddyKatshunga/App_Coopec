<div x-data="{
    showEcheancier: false,
    formatDate(dateStr) {
        if(!dateStr) return '';
        const d = new Date(dateStr);
        return d.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
    },
    getNextDate(startDate, step, unit) {
        let date = new Date(startDate);
        if (unit === 'jour') {
            let added = 0;
            while (added < step) {
                date.setDate(date.getDate() + 1);
                if (date.getDay() !== 0) added++; 
            }
        } else if (unit === 'semaine') {
            date.setDate(date.getDate() + (step * 7));
        } else if (unit === 'mois') {
            date.setMonth(date.getMonth() + step);
        } else if (unit === 'annee') {
            date.setFullYear(date.getFullYear() + step);
        }
        return date;
    },
    get schedule() {
        let rows = [];
        let cap = parseFloat($wire.capital) || 0;
        let int = parseFloat($wire.interet) || 0;
        let dur = parseInt($wire.duree) || 0;
        let unit = $wire.unite_temps;
        let start = new Date($wire.date_credit);

        if (dur <= 0 || isNaN(start.getTime())) return rows;

        for (let i = 1; i <= dur; i++) {
            let dueDate = this.getNextDate(start, i, unit);
            rows.push({
                date: this.formatDate(dueDate),
                capital: (cap / dur).toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2}),
                interet: (int / dur).toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2}),
                total: ((cap + int) / dur).toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2}),
                rawDate: dueDate.toISOString().split('T')[0]
            });
        }
        
        // Mise à jour de la date de fin dans Livewire si elle n'est pas modifiée manuellement
        if(rows.length > 0) {
            $wire.date_fin = rows[rows.length - 1].rawDate;
        }

        return rows;
    }
}" class="mt-4">
    
    <button type="button" 
            @click="showEcheancier = !showEcheancier"
            class="flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
        <span x-text="showEcheancier ? '🙈 Masquer l\'échéancier' : '👁️ Afficher l\'échéancier prévisionnel'"></span>
        <svg :class="showEcheancier ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </button>

    <div x-show="showEcheancier" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="mt-4 border rounded-xl overflow-hidden shadow-sm bg-gray-50">
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Échéance</th>
                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Capital</th>
                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Intérêt</th>
                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <template x-for="(item, index) in schedule" :key="index">
                    <tr class="hover:bg-blue-50">
                        <td class="px-4 py-2 text-sm text-gray-700 font-medium" x-text="item.date"></td>
                        <td class="px-4 py-2 text-sm text-right text-gray-600" x-text="item.capital"></td>
                        <td class="px-4 py-2 text-sm text-right text-gray-600" x-text="item.interet"></td>
                        <td class="px-4 py-2 text-sm text-right font-bold text-blue-700" x-text="item.total"></td>
                    </tr>
                </template>
            </tbody>
            <tfoot class="bg-gray-50 font-bold">
                <tr>
                    <td class="px-4 py-2 text-sm text-gray-700">TOTAL</td>
                    <td class="px-4 py-2 text-sm text-right" x-text="(parseFloat($wire.capital) || 0).toLocaleString('fr-FR', {minimumFractionDigits: 2})"></td>
                    <td class="px-4 py-2 text-sm text-right" x-text="(parseFloat($wire.interet) || 0).toLocaleString('fr-FR', {minimumFractionDigits: 2})"></td>
                    <td class="px-4 py-2 text-sm text-right text-blue-700" x-text="((parseFloat($wire.capital) || 0) + (parseFloat($wire.interet) || 0)).toLocaleString('fr-FR', {minimumFractionDigits: 2})"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>