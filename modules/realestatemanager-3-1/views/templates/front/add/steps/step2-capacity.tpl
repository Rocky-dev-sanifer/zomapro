{*
* Étape 2 — Capacités (chambres, toilettes, parkings)
*}
<div
  class="re-step-panel"
  data-panel="2"
>
  <div class="re-card">

    <div class="re-capacities">

      <div class="re-capacity">
        <label>
          <i
            class="re-capacity-icon"
            width="20"
            height="20"
            data-lucide="bed-double"
          ></i>
          CHAMBRES
        </label>
        <div class="re-counter-input">
          <button
            type="button"
            class="re-counter-btn"
            data-target="re-bedrooms"
            data-delta="-1"
            data-min="0"
          >
            <i
              data-lucide="minus"
              width="20"
              height="20"
            ></i>
          </button>
          <input
            type="number"
            name="bedrooms"
            id="re-bedrooms"
            min="0"
            value="{if $property}{$property->bedrooms|intval}{else}0{/if}"
          >
          <button
            type="button"
            class="re-counter-btn"
            data-target="re-bedrooms"
            data-delta="1"
          >
            <i
              data-lucide="plus"
              width="20"
              height="20"
            ></i>
          </button>
        </div>
      </div>

      <div class="re-capacity">
        <label>
          <i
            class="re-capacity-icon"
            width="20"
            height="20"
            data-lucide="bath"
          ></i>
          TOILETTES
        </label>
        <div class="re-counter-input">
          <button
            type="button"
            class="re-counter-btn"
            data-target="re-toilets"
            data-delta="-1"
            data-min="0"
          >
            <i
              data-lucide="minus"
              width="20"
              height="20"
            ></i>
          </button>
          <input
            type="number"
            name="toilets"
            id="re-toilets"
            min="0"
            value="{if $property}{$property->toilets|intval}{else}0{/if}"
          >
          <button
            type="button"
            class="re-counter-btn"
            data-target="re-toilets"
            data-delta="1"
          >
            <i
              data-lucide="plus"
              width="20"
              height="20"
            ></i>
          </button>
        </div>
      </div>

      <div class="re-capacity">
        <label>
          <i
            class="re-capacity-icon"
            width="20"
            height="20"
            data-lucide="car"
          ></i>
          PARKINGS
        </label>
        <div class="re-counter-input">
          <button
            type="button"
            class="re-counter-btn"
            data-target="re-parkings"
            data-delta="-1"
            data-min="0"
          >
            <i
              data-lucide="minus"
              width="20"
              height="20"
            ></i>
          </button>
          <input
            type="number"
            name="parkings"
            id="re-parkings"
            min="0"
            value="{if $property}{$property->parkings|intval}{else}0{/if}"
          >
          <button
            type="button"
            class="re-counter-btn"
            data-target="re-parkings"
            data-delta="1"
          >
            <i
              data-lucide="plus"
              width="20"
              height="20"
            ></i>
          </button>
        </div>
      </div>

    </div>

  </div>
</div>