<?php
function bsp_form_shortcode($atts) {
    $atts = shortcode_atts(['id' => 0], $atts);
    $competition_id = intval($atts['id']);

    ob_start(); ?>
    <div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow-lg mt-8" x-data="boulderForm(<?php echo $competition_id; ?>)" x-init="loadBoulders()">
        <h2 class="text-2xl font-semibold mb-6 text-center">Въведи резултат</h2>

        <div class="mb-4">
            <label class="block mb-1 font-medium text-gray-700">Избери боулдър:</label>
            <select x-model="boulderId" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <option value="">-- Избери --</option>
                <template x-for="b in boulders" :key="b.id">
                    <option :value="b.id" x-text="b.title"></option>
                </template>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4 text-center mb-6">
            <div>
                <label class="flex flex-col items-center cursor-pointer">
                    <div class="w-20 h-20 bg-blue-100 border-4 border-blue-500 rounded-full flex items-center justify-center shadow-md">
                        <input type="checkbox" x-model="zone" class="w-8 h-8 text-blue-600 focus:ring-0 rounded cursor-pointer">
                    </div>
                    <span class="mt-2 text-lg font-semibold text-blue-700">Зона</span>
                </label>
                <input type="number" x-model="zoneAttempts" class="mt-2 w-24 text-center border border-gray-300 rounded py-1" placeholder="Опити">
            </div>
            <div>
                <label class="flex flex-col items-center cursor-pointer">
                    <div class="w-20 h-20 bg-green-100 border-4 border-green-500 rounded-full flex items-center justify-center shadow-md">
                        <input type="checkbox" x-model="top" class="w-8 h-8 text-green-600 focus:ring-0 rounded cursor-pointer">
                    </div>
                    <span class="mt-2 text-lg font-semibold text-green-700">Топ</span>
                </label>
                <input type="number" x-model="topAttempts" class="mt-2 w-24 text-center border border-gray-300 rounded py-1" placeholder="Опити">
            </div>
        </div>

        <button @click.prevent="submit()" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition duration-200">Изпрати</button>
        <p x-text="message" class="mt-4 text-center text-green-600 font-medium"></p>
    </div>

    <script>
    function boulderForm(competitionId) {
      return {
        competitionId,
        boulders: [],
        boulderId: '',
        zone: false,
        top: false,
        zoneAttempts: 1,
        topAttempts: 1,
        message: '',
        async loadBoulders() {
          const res = await fetch('/wp-json/bsp/v1/boulders/' + this.competitionId);
          this.boulders = await res.json();
        },
        async submit() {
          const response = await fetch('/wp-json/bsp/v1/submit', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Content-Type': 'application/json',
              'X-WP-Nonce': bspData.nonce
            },
            body: JSON.stringify({
              boulder: this.boulderId,
              zone: this.zone,
              top: this.top,
              zone_attempts: this.zoneAttempts,
              top_attempts: this.topAttempts,
              competition_id: this.competitionId
            })
          });
          const result = await response.json();
          this.message = result.success ? 'Успешно записано!' : 'Грешка при запис!';
        }
      };
    }
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('bsp_result_form', 'bsp_form_shortcode');
