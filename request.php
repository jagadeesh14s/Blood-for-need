<?php
session_start();
// You can handle any session variables or processing here if needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Select Blood Group</title>
  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      background-image: url('bfn1.jpg'); /* Background image */
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }

    .container {
      background-color: rgba(255, 255, 255, 0.1); /* Transparent background */
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
      width: 300px;
      text-align: center;
      margin: 20px;
    }

    h2 {
      color: #b22222;
      margin-bottom: 20px;
    }

    select, button {
      width: 100%;
      padding: 15px;
      margin: 15px 0;
      border-radius: 5px;
      border: 1px solid #ddd;
      font-size: 18px;
    }

    .blood-group {
      display: inline-block;
      padding: 12px 24px;
      margin: 10px;
      border-radius: 25px;
      background-color: rgba(247, 249, 252, 0.2);
      border: 2px solid #ddd;
      cursor: pointer;
      transition: all 0.3s;
    }

    .blood-group.selected {
      border-color: #b22222;
      background-color: rgba(255, 234, 234, 0.8);
    }

    .blood-group.disabled {
      background-color: rgba(224, 224, 224, 0.8);
      border-color: #ddd;
      cursor: not-allowed;
    }

    button {
      background-color: #b22222;
      color: white;
      border: none;
      font-size: 18px;
    }

    button:hover {
      background-color: #a12020;
    }

    #selectedBloodType {
      margin-top: 15px;
      font-size: 18px;
      color: #b22222;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Select Blood Group</h2>

  <!-- Patient Blood Group Selection -->
  <label for="patientBloodGroup">Patient Blood Group:</label>
  <select id="patientBloodGroup" onchange="autoSelectBloodGroups()">
    <option value="">Select Patient Blood Group</option>
    <option value="A+">A+</option>
    <option value="A-">A-</option>
    <option value="B+">B+</option>
    <option value="B-">B-</option>
    <option value="O+">O+</option>
    <option value="O-">O-</option>
    <option value="AB+">AB+</option>
    <option value="AB-">AB-</option>
  </select>

  <!-- Display Selected Blood Type -->
  <div id="selectedBloodType">Requested Blood Type: None</div>

  <!-- Blood Group Options -->
  <div id="bloodGroups">
    <div class="blood-group" data-group="A+" onclick="toggleSelect(this)">A+</div>
    <div class="blood-group" data-group="A-" onclick="toggleSelect(this)">A-</div>
    <div class="blood-group" data-group="B+" onclick="toggleSelect(this)">B+</div>
    <div class="blood-group" data-group="B-" onclick="toggleSelect(this)">B-</div>
    <div class="blood-group" data-group="O+" onclick="toggleSelect(this)">O+</div>
    <div class="blood-group" data-group="O-" onclick="toggleSelect(this)">O-</div>
    <div class="blood-group" data-group="AB+" onclick="toggleSelect(this)">AB+</div>
    <div class="blood-group" data-group="AB-" onclick="toggleSelect(this)">AB-</div>
  </div>

  <!-- Confirmation Button -->
  <button onclick="confirmSelection()">Confirm</button>

  <!-- Hidden Form to Submit Data -->
  <form id="bloodGroupForm" action="send_message.php" method="POST" style="display:none;">
    <input type="hidden" name="selected_groups" id="selectedGroupsInput">
  </form>
</div>

<script>
  const compatibleBloodGroups = {
    'A+': ['A+', 'A-', 'O+', 'O-'],
    'A-': ['A-', 'O-'],
    'B+': ['B+', 'B-', 'O+', 'O-'],
    'B-': ['B-', 'O-'],
    'O+': ['O+', 'O-'],
    'O-': ['O-'],
    'AB+': ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'],
    'AB-': ['A-', 'B-', 'O-', 'AB-']
  };

  function autoSelectBloodGroups() {
    const patientBloodGroup = document.getElementById('patientBloodGroup').value;
    const bloodGroupElements = document.querySelectorAll('.blood-group');

    // Update the displayed selected blood type
    document.getElementById('selectedBloodType').innerText = `Requested Blood Type: ${patientBloodGroup || 'None'}`;

    bloodGroupElements.forEach(el => {
      const group = el.getAttribute('data-group');
      el.classList.remove('disabled', 'selected');
      
      if (compatibleBloodGroups[patientBloodGroup]?.includes(group)) {
        el.classList.add('selected');
      } else {
        el.classList.add('disabled');
      }
    });
  }

  function toggleSelect(el) {
    if (!el.classList.contains('disabled')) {
      el.classList.toggle('selected');
    }
  }

  function confirmSelection() {
    const selectedGroups = Array.from(document.querySelectorAll('.blood-group.selected'))
      .map(el => el.getAttribute('data-group'));
    
    if (selectedGroups.length > 0) {
      document.getElementById('selectedGroupsInput').value = selectedGroups.join(',');
      document.getElementById('bloodGroupForm').submit();
    } else {
      alert('Please select a compatible blood group.');
    }
  }
</script>

</body>
</html>
