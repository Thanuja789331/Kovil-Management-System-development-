        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-primary-900/90 backdrop-blur-md text-white py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm">© <?= date("Y") ?> <?= APP_NAME ?>. <?= getCurrentLanguage() === 'ta' ? 'அனைத்து உரிமைகளும் பாதுகாக்கப்பட்டவை.' : 'All rights reserved.' ?></p>
            <p class="text-xs mt-2 text-gray-400"><?= getCurrentLanguage() === 'ta' ? 'பக்தியுடன் உருவாக்கப்பட்டது 🙏' : 'Developed with devotion 🙏' ?></p>
        </div>
    </footer>
    <?php if (getCurrentLanguage() === 'ta'): ?>
    <?php
        $translations = require __DIR__ . '/../../../config/translations.php';
        $englishTamilMap = [];
        foreach ($translations as $entry) {
            if (!empty($entry['en']) && !empty($entry['ta'])) {
                $englishTamilMap[$entry['en']] = $entry['ta'];
            }
        }
    ?>
    <script>
    (function () {
        const map = <?= json_encode($englishTamilMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

        function translateExact(text) {
            const trimmed = (text || '').trim();
            if (!trimmed) return text;
            if (Object.prototype.hasOwnProperty.call(map, trimmed)) {
                return text.replace(trimmed, map[trimmed]);
            }
            return text;
        }

        function walk(node) {
            if (!node) return;
            if (node.nodeType === Node.TEXT_NODE) {
                node.textContent = translateExact(node.textContent);
                return;
            }
            if (node.nodeType !== Node.ELEMENT_NODE) return;

            if (node.hasAttribute('placeholder')) {
                const placeholder = node.getAttribute('placeholder');
                if (map[placeholder]) node.setAttribute('placeholder', map[placeholder]);
            }
            if ((node.tagName === 'INPUT' || node.tagName === 'BUTTON') && node.hasAttribute('value')) {
                const value = node.getAttribute('value');
                if (map[value]) node.setAttribute('value', map[value]);
            }

            for (const child of node.childNodes) {
                walk(child);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            walk(document.body);
        });
    })();
    </script>
    <?php endif; ?>
</body>
</html>
