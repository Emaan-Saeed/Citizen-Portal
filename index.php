<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "citizen_portal");
if (!$conn) { die("Database connection failed"); }

// ---------- SIGNUP ----------
if (isset($_POST['action']) && $_POST['action'] === 'signup') {
    $name = $_POST['name'];
    $cnic = $_POST['cnic'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) { echo "EMAIL_EXISTS"; exit; }

    mysqli_query($conn, "INSERT INTO users (fullname, cnic, email, phone, password, city)
        VALUES ('$name','$cnic','$email','$phone','$password','$city')");

    echo "SIGNUP_OK"; exit;
}

// ---------- LOGIN ----------
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($res);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['fullname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['city'] = $user['city'];
        echo "LOGIN_OK";
    } else { echo "LOGIN_FAIL"; }
    exit;
}

// ---------- LOGOUT ----------
if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    session_destroy(); echo "LOGOUT_OK"; exit;
}

// ---------- COMPLAINT ----------
if (isset($_POST['action']) && $_POST['action'] === 'complaint') {
    if (!isset($_SESSION['user_id'])) { echo "LOGIN_REQUIRED"; exit; }

    $uid = $_SESSION['user_id'];
    $category = $_POST['category'];
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $priority = $_POST['priority'];
    $fileName = "";

    if(!file_exists('uploads')){ mkdir('uploads', 0777, true); }

    if(isset($_FILES['file']) && $_FILES['file']['name'] != "") {
        $fileName = time() . "_" . basename($_FILES['file']['name']);
        if(!move_uploaded_file($_FILES['file']['tmp_name'], "uploads/".$fileName)){
            echo "ERROR_UPLOADING_FILE"; exit;
        }
    }

    $insert = mysqli_query($conn, "INSERT INTO complaints 
        (user_id, category, subject, description, location, priority, file, status)
        VALUES ('$uid','$category','$subject','$description','$location','$priority','$fileName','Pending')");

    if($insert){ echo "COMPLAINT_OK"; }
    else { echo "DB_INSERT_ERROR: " . mysqli_error($conn); }
    exit;
}

// ---------- FETCH COMPLAINTS (JSON for status panel) ----------
if(isset($_GET['get_complaints']) && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $res = mysqli_query($conn, "SELECT * FROM complaints WHERE user_id='$uid' ORDER BY created_at DESC");
    $complaints = [];
    while($row = mysqli_fetch_assoc($res)) { $complaints[] = $row; }
    echo json_encode($complaints); exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pakistan Citizen Portal</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Roboto',sans-serif;}
body{background:linear-gradient(135deg,#01411C 0%,#0C6D3A 100%);min-height:100vh;color:#333;}
.container{max-width:1200px;margin:40px auto;padding:0 20px;}
h2{color:#01411C;margin-bottom:20px;font-size:32px;}
.header{background:white;padding:15px 0;box-shadow:0 3px 15px rgba(0,0,0,0.2);}
.header-content{max-width:1200px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;}
.logo img{height:50px;margin-right:10px;}
.nav{display:flex;gap:20px;}
.nav button{background:none;border:none;color:#01411C;font-size:16px;cursor:pointer;padding:8px 16px;border-radius:8px;transition:0.3s;}
.nav button:hover,.nav button.active{background:#01411C;color:white;}
.page{display:none;background:white;padding:40px;border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,0.2);}
.page.active{display:block;animation:fadeIn 0.5s;}
@keyframes fadeIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
.form-group{margin-bottom:20px;}
.form-group label{display:block;margin-bottom:8px;font-weight:500;}
.form-group input,.form-group textarea,.form-group select{width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;font-size:16px;}
.form-group textarea{resize:vertical;min-height:120px;}
.btn{background:#01411C;color:white;padding:12px 25px;border:none;border-radius:8px;cursor:pointer;transition:0.3s;}
.btn:hover{background:#0C6D3A;}
.logout-btn{background:#dc3545;float:right;}.logout-btn:hover{background:#c82333;}
.user-info{background:#e8f5e9;padding:15px;border-radius:10px;margin-bottom:20px;}
.complaint-list{margin-top:30px;display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;}
.complaint-item{background:#f8f9fa;padding:20px;border-radius:10px;border-left:6px solid #01411C;transition:0.3s;}
.complaint-item.pending{border-left:6px solid orange;}
.complaint-item.resolved{border-left:6px solid green;background:#e8f5e9;}
.complaint-item h4{color:#01411C;margin-bottom:10px;}
.contact-section{margin-top:40px;background:#f1f1f1;padding:40px;border-radius:15px;}
.contact-section h3{color:#01411C;margin-bottom:15px;}
.contact-section p{margin-bottom:10px;}
</style>
</head>
<body>

<div class="header">
  <div class="header-content">
    <div class="logo">
      <img src="https://upload.wikimedia.org/wikipedia/commons/3/32/Flag_of_Pakistan.svg" alt="Logo">
      <h1>Pakistan Citizen Portal</h1>
    </div>
    <nav class="nav">
      <button onclick="showPage('home')" id="nav-home" class="active">Home</button>
      <button onclick="showPage('signup')" id="nav-signup">Sign Up</button>
      <button onclick="showPage('login')" id="nav-login">Login</button>
      <button onclick="showPage('complaints')" id="nav-complaints" style="display:none;">Complaints</button>
    </nav>
  </div>
</div>

<div class="container">

<!-- Home -->
<div id="home" class="page active">
  <h2>Welcome to Pakistan Citizen Portal</h2>
  <p>Your voice matters. Lodge complaints, track progress, and engage with government services efficiently.</p>
  <div class="contact-section">
    <h3>Contact Us</h3>
    <p>Email: support@citizenportal.pk</p>
    <p>Phone: +92 300 1234567</p>
    <p>Address: Islamabad, Pakistan</p>
  </div>
</div>

<!-- Sign Up -->
<div id="signup" class="page">
  <h2>Create Your Account</h2>
  <form onsubmit="handleSignup(event)">
    <div class="form-group"><label>Full Name *</label><input type="text" id="signup-name" name="name" required></div>
    <div class="form-group"><label>CNIC *</label><input type="text" id="signup-cnic" name="cnic" required></div>
    <div class="form-group"><label>Email *</label><input type="email" id="signup-email" name="email" required></div>
    <div class="form-group"><label>Phone *</label><input type="tel" id="signup-phone" name="phone" required></div>
    <div class="form-group"><label>Password *</label><input type="password" id="signup-password" name="password" required></div>
    <div class="form-group"><label>City *</label><input type="text" id="signup-city" name="city" required></div>
    <button type="submit" class="btn">Sign Up</button>
  </form>
</div>

<!-- Login -->
<div id="login" class="page">
  <h2>Login</h2>
  <form onsubmit="handleLogin(event)">
    <div class="form-group"><label>Email *</label><input type="email" id="login-email" name="email" required></div>
    <div class="form-group"><label>Password *</label><input type="password" id="login-password" name="password" required></div>
    <button type="submit" class="btn">Login</button>
  </form>
</div>

<!-- Complaints -->
<div id="complaints" class="page">
  <div id="user-info" class="user-info"></div>
  <button onclick="logout()" class="btn logout-btn">Logout</button>
  <h2>Submit Complaint</h2>
  <form id="complaint-form" enctype="multipart/form-data" onsubmit="handleComplaint(event)">
    <div class="form-group"><label>Category *</label>
      <select id="complaint-category" name="category" required>
        <option value="">Select Category</option>
        <option value="Road">Road Infrastructure</option>
        <option value="Water">Water Supply</option>
        <option value="Electricity">Electricity</option>
        <option value="Healthcare">Healthcare</option>
        <option value="Education">Education</option>
        <option value="Other">Other</option>
      </select>
    </div>
    <div class="form-group"><label>Subject *</label><input type="text" id="complaint-subject" name="subject" required></div>
    <div class="form-group"><label>Description *</label><textarea id="complaint-description" name="description" required></textarea></div>
    <div class="form-group"><label>Location *</label><input type="text" id="complaint-location" name="location" required></div>
    <div class="form-group"><label>Priority *</label>
      <select id="complaint-priority" name="priority" required>
        <option value="Low">Low</option>
        <option value="Medium" selected>Medium</option>
        <option value="High">High</option>
      </select>
    </div>
    <div class="form-group"><label>File (Optional)</label><input type="file" id="complaint-file" name="file"></div>
    <button type="submit" class="btn">Submit Complaint</button>
  </form>
  <h2>Your Complaints & Status</h2>
  <div id="complaint-status" class="complaint-list"></div>
</div>

</div>

<script>
function showPage(pageName){
    document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
    document.querySelectorAll('.nav button').forEach(b=>b.classList.remove('active'));
    document.getElementById(pageName).classList.add('active');
    document.getElementById('nav-'+pageName)?.classList.add('active');
    if(pageName==='complaints') displayUserInfo();
}

function handleSignup(e){
    e.preventDefault();
    let formData = new FormData();
    formData.append('action','signup');
    formData.append('name', document.getElementById('signup-name').value);
    formData.append('cnic', document.getElementById('signup-cnic').value);
    formData.append('email', document.getElementById('signup-email').value);
    formData.append('phone', document.getElementById('signup-phone').value);
    formData.append('password', document.getElementById('signup-password').value);
    formData.append('city', document.getElementById('signup-city').value);
    fetch('index.php',{method:'POST',body:formData})
    .then(res=>res.text()).then(res=>{
        if(res==='SIGNUP_OK'){alert('Signup successful!'); showPage('login');}
        else alert('Error: '+res);
    });
}

function handleLogin(e){
    e.preventDefault();
    const data = new URLSearchParams({action:'login', email:document.getElementById('login-email').value, password:document.getElementById('login-password').value});
    fetch('index.php',{method:'POST',body:data,credentials:'same-origin'})
    .then(res=>res.text()).then(res=>{
        if(res==='LOGIN_OK'){alert('Login successful!'); location.reload();}
        else alert('Invalid email or password');
    });
}

function logout(){
    let formData = new FormData(); formData.append('action','logout');
    fetch('index.php',{method:'POST',body:formData}).then(()=>location.reload());
}

function handleComplaint(e){
    e.preventDefault();
    const form = document.getElementById('complaint-form');
    const data = new FormData(form);
    data.append('action','complaint');
    fetch('index.php',{method:'POST',body:data,credentials:'same-origin'})
    .then(res=>res.text()).then(res=>{
        if(res==='COMPLAINT_OK'){alert('Complaint submitted successfully!'); form.reset(); displayComplaints();}
        else alert('Error submitting complaint: '+res);
    });
}

function displayUserInfo(){
    <?php if(isset($_SESSION['user_id'])): ?>
        document.getElementById('user-info').innerHTML = '<strong>Welcome, <?php echo $_SESSION['name']; ?></strong> | Email: <?php echo $_SESSION['email']; ?> | City: <?php echo $_SESSION['city']; ?>';
        document.getElementById('nav-signup').style.display='none';
        document.getElementById('nav-login').style.display='none';
        document.getElementById('nav-complaints').style.display='block';
        displayComplaints();
    <?php endif; ?>
}

function displayComplaints(){
    fetch('index.php?get_complaints=1',{credentials:'same-origin'})
    .then(res=>res.json())
    .then(data=>{
        let html='';
        data.forEach(c=>{
            html+='<div class="complaint-item '+c.status.toLowerCase()+'">';
            html+='<h4>'+c.subject+' ('+c.status+')</h4>';
            html+='<p><strong>Category:</strong> '+c.category+'</p>';
            html+='<p>'+c.description+'</p>';
            html+='<p><strong>Location:</strong> '+c.location+' | <strong>Priority:</strong> '+c.priority+'</p>';
            if(c.file) html+='<p><a href="uploads/'+c.file+'" target="_blank">View File</a></p>';
            html+='<p><small>Submitted on: '+c.created_at+'</small></p>'; html+='</div>';
        });
        document.getElementById('complaint-status').innerHTML = html;
    });
}

window.onload = displayUserInfo;
</script>

</body>
</html>
