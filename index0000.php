<?php
// Simple index that serves the UI. Keep PHP minimal here.
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Simple CRUD Template</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
  <main class="container">
    <h1>Contacts</h1>

    <section class="form-wrap">
      <h2>Add / Edit Contact</h2>
      <form id="contactForm">
        <input type="hidden" id="contactId" name="id" value="">
        <label>
          Name
          <input type="text" id="name" name="name" required>
        </label>
        <label>
          Email
          <input type="email" id="email" name="email" required>
        </label>
        <label>
          Phone
          <input type="text" id="phone" name="phone">
        </label>
        <div class="buttons">
          <button type="submit">Save</button>
          <button type="button" id="resetBtn">Reset</button>
        </div>
      </form>
    </section>

    <section class="list-wrap">
      <h2>All Contacts</h2>
      <div id="message" aria-live="polite"></div>
      <table id="contactsTable">
        <thead>
          <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Actions</th></tr>
        </thead>
        <tbody></tbody>
      </table>
    </section>
  </main>

  <script src="assets/js/main.js" defer></script>
</body>
</html>
