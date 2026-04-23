<section class="card" style="max-width: 860px;">
    <h1>Submit a nomination</h1>
    <p class="muted">This mirrors the nomination flow from the original application and writes directly to the `nominations` table.</p>

    <?php if (!empty($flash)): ?>
        <div class="notice section"><?= e($flash) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= e(url('/nominate')) ?>" class="section">
        <div class="grid">
            <div class="form-row">
                <label for="nominatorName">Your name</label>
                <input id="nominatorName" name="nominatorName" required>
            </div>
            <div class="form-row">
                <label for="nominatorEmail">Your email</label>
                <input id="nominatorEmail" name="nominatorEmail" required>
            </div>
            <div class="form-row">
                <label for="nomineeName">Nominee name</label>
                <input id="nomineeName" name="nomineeName" required>
            </div>
            <div class="form-row">
                <label for="nomineeEmail">Nominee email</label>
                <input id="nomineeEmail" name="nomineeEmail">
            </div>
            <div class="form-row">
                <label for="nominationType">Type</label>
                <select id="nominationType" name="nominationType">
                    <option value="ORGANISATION">Organisation</option>
                    <option value="LEADER">Leader</option>
                    <option value="INDIVIDUAL">Individual</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <label for="reason">Reason</label>
            <textarea id="reason" name="reason" rows="6" required></textarea>
        </div>
        <button type="submit">Submit nomination</button>
    </form>
</section>
