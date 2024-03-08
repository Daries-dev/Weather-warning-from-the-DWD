/**
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */

import * as Core from "WoltLabSuite/Core/Core";
import { SearchInputOptions } from "WoltLabSuite/Core/Ui/Search/Data";
import UiSearchInput from "WoltLabSuite/Core/Ui/Search/Input";

class UiWeatherWarningRegionSearchInput extends UiSearchInput {
  constructor(element: HTMLInputElement, options: SearchInputOptions) {
    options = Core.extend(
      {
        ajax: {
          className: "wcf\\data\\weather\\warning\\region\\WeatherWarningRegionAction",
        },
      },
      options,
    ) as any;

    super(element, options);
  }
}

export = UiWeatherWarningRegionSearchInput;
