<section class="confirm-alert-page">

    <h3>Configuration Summary</h3>

    <div class="summary-grid" id="alertSummary">
    </div>

    <div id="alertError" class="alert alert-danger d-none mt-3" role="alert">
    </div>
    <!-- IMPORTANT: no action attribute -->
    <form id="confirmForm" novalidate>

        <label>Alert Name <span class="optional">(Optional)</span>
       </label>
        <input type="text"
               id="alertName"
               name="alert_name"
               placeholder="e.g. Heatwave Warning">

        <div class="form-actions">
            <button 
                  type="button"
                    class="btn btn-light"
                   id="backToCreate">
                ← Back
            </button>

            <button type="button"
                    id="confirmCreateAlert"
                    class="btn btn-primary">
                Create Alert
            </button>
        </div>

    </form>

</section>


