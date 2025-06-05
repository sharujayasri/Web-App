<?php
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cadiolume_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// If request is AJAX (Profile Update)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["name"])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone_number = htmlspecialchars($_POST['phone_number']);

    $update_sql = "UPDATE user SET username=?, email=?, mobile=? WHERE id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssi", $name, $email, $phone_number, $user_id);

    if ($stmt->execute()) {
        $response = ["success" => true, "message" => "Profile updated successfully!"];
    } else {
        $response = ["success" => false, "message" => "Profile update failed!"];
    }
    $stmt->close();

    // Handle Profile Picture Upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $imageFileType = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        $new_filename = "profile_" . $user_id . "." . $imageFileType;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
            $update_img_sql = "UPDATE user SET profile=? WHERE id=?";
            $stmt = $conn->prepare($update_img_sql);
            $stmt->bind_param("si", $target_file, $user_id);
            if ($stmt->execute()) {
                $response["image"] = $target_file;
            }
            $stmt->close();
        }
    }

    echo json_encode($response);
    exit();
}

// Fetch user details for page load
$sql = "SELECT username, email, mobile, profile FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $name = htmlspecialchars($user['username']);
    $email = htmlspecialchars($user['email']);
    $phone_number = htmlspecialchars($user['mobile']);
    $profile_pic = !empty($user['profile']) ? $user['profile'] : 'image/default.png';
} else {
    header("Location: login.php");
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile - CardioLume</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-r from-[#ff9a9e] to-[#fad0c4]">


  <!-- Logo Header -->
  <header class="absolute top-5 left-5 flex items-center">
    <img src="../image/Red_Simple_Medical_Health_Logo_3-removebg-preview.png" alt="CardioLume Logo" class="w-20 h-20">
    <h1 class="text-sm font-bold text-red-800 ml-2">CardioLume</h1>
  </header>

  <!-- Profile Card -->
  <div class="bg-white p-6 md:p-10 rounded-2xl shadow-lg w-[90%] max-w-md border border-black text-center">
    <h2 class="text-2xl md:text-3xl font-bold text-red-800 mb-6">MY PROFILE</h2>

    <!-- Profile Picture Upload -->
    <div class="flex flex-col items-center mb-4">
      <img id="profile-preview" src="<?php echo $profile_pic; ?>" alt="Profile Picture"
           class="w-28 h-28 md:w-32 md:h-32 rounded-full border-4 border-red-500 shadow-lg">
      <input type="file" name="profile_pic" id="profile_pic"
             class="mt-3 text-xs md:text-sm" accept="image/*" onchange="previewImage(event)">
    </div>

    <!-- Profile Form -->
    <form id="profile-form" class="space-y-3 md:space-y-4 text-left text-sm md:text-base" enctype="multipart/form-data">
      <label class="font-semibold">Name</label>
      <input type="text" id="name" name="name"
             class="w-full px-3 py-2 border border-black rounded-lg text-sm md:text-base"
             placeholder="Enter username" value="<?php echo $name; ?>">

      <label class="font-semibold">Email Id</label>
      <input type="email" id="email" name="email"
             class="w-full px-3 py-2 border border-black rounded-lg text-sm md:text-base"
             placeholder="Enter Your Email Id" value="<?php echo $email; ?>">

      <label class="font-semibold">Phone Number</label>
      <input type="text" id="phone_number" name="phone_number"
             class="w-full px-3 py-2 border border-black rounded-lg text-sm md:text-base"
             placeholder="Enter Phone Number" value="<?php echo $phone_number; ?>">

      <button type="submit"
              class="w-full bg-green-600 hover:bg-green-700 text-white py-2 md:py-3 rounded-lg text-base font-semibold shadow-lg">
        UPDATE PROFILE
      </button>
    </form>

    <!-- Feedback Message -->
    <div id="message" class="mt-4 text-sm md:text-lg font-semibold text-red-500"></div>
  </div>

  <!-- JS: Preview Image + AJAX Submit -->
  <script>
    function previewImage(event) {
      var reader = new FileReader();
      reader.onload = function () {
        document.getElementById('profile-preview').src = reader.result;
      };
      reader.readAsDataURL(event.target.files[0]);
    }

    $(document).ready(function () {
      $("#profile-form").on("submit", function (event) {
        event.preventDefault();

        var formData = new FormData(this);
        formData.append("profile_pic", $("#profile_pic")[0].files[0]);

        $.ajax({
          url: "profil.php",
          type: "POST",
          data: formData,
          dataType: "json",
          contentType: false,
          processData: false,
          success: function (response) {
            if (response.success) {
              $("#message").text(response.message).removeClass('text-red-500').addClass('text-green-500');
              if (response.image) {
                $("#profile-preview").attr("src", response.image + "?" + new Date().getTime());
              }
              setTimeout(function () {
                window.location.href = "aboutous.html";
              }, 1500);
            } else {
              $("#message").text(response.message).removeClass('text-green-500').addClass('text-red-500');
            }
          },
          error: function () {
            $("#message").text("An error occurred. Please try again.").removeClass('text-green-500').addClass('text-red-500');
          }
        });
      });
    });
  </script>

</body>
</html>
