/**
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.dev - Free License <https://daries.dev/en/license-for-free-plugins>
 */
define(["require", "exports", "WoltLabSuite/Core/Component/Dialog", "WoltLabSuite/Core/Helper/Selector"], function (require, exports, Dialog_1, Selector_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = void 0;
    function setup() {
        (0, Selector_1.wheneverFirstSeen)("[data-weather-info-full-view]", (element) => {
            element.addEventListener("click", () => {
                showDialog(element);
            });
        });
    }
    exports.setup = setup;
    function showDialog(element) {
        const dialog = (0, Dialog_1.dialogFactory)().fromHtml(element.innerHTML).withoutControls();
        dialog.show(element.dataset.weatherInfoFullView);
    }
});
