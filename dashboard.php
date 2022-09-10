<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}
function escape($html) {
    return htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}
if (isset($_POST['submit'])) {
    try {
      require "config.php";
      $dsn = "mysql:host=localhost;dbname=test";
      $options = array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
      );
      // connection using PHP Data Object
      $link2 = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
      $sql = "SELECT *
      FROM phonebook
      WHERE firstname = :firstname";
  
      $firstname = $_POST['firstname'];
  
      $statement = $link2->prepare($sql);
      $statement->bindParam(':firstname', $firstname, PDO::PARAM_STR);
      $statement->execute();
  
      $result = $statement->fetchAll();
    } catch(PDOException $error) {
      echo $sql . "<br>" . $error->getMessage();
    }
  }
?>
<html>
    <head>
        <title>
            Dashboard
</title>
</head>
<meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" 
href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="Styles.css">
 <script 
src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
 <script 
src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></scrip
t>
 <script 
src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<body style="background-color:#303030;color:white;">
<div style="background-color:#212121">
<button type="button" class = "TopButton" onclick="location.href='index.php'">Homepage</button>
<button type="button" class="TopButton" onclick="location.href='logout.php'" style="float:right">Logout</button>
<br>
</div>
<div>
<h1> Welcome <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>, to your dashboard </h1>
<br><br>
</div>
<?php
if (isset($_POST['submit'])) {
  if ($result && $statement->rowCount() > 0) { ?>
    <center><h2>Results:</h2></center><br>

    <table class = "table table-striped table-dark">
      <thead>
<tr>
  <th>#</th>
  <th>First Name</th>
  <th>Last Name</th>
  <th>Email Address</th>
  <th>Age</th>
  <th>Location</th>
  <th>Date</th>
</tr>
      </thead>
      <tbody>
  <?php foreach ($result as $row) { ?>
      <tr>
<td><?php echo escape($row["id"]); ?></td>
<td><?php echo escape($row["firstname"]); ?></td>
<td><?php echo escape($row["lastname"]); ?></td>
<td><?php echo escape($row["email"]); ?></td>
<td><?php echo escape($row["age"]); ?></td>
<td><?php echo escape($row["location"]); ?></td>
<td><?php echo escape($row["date"]); ?> </td>
      </tr>
    <?php } ?>
      </tbody>
  </table>
  <?php } else { ?>
     <h3>No results found for <?php echo escape($_POST['firstname']); ?>.</h3>
  <?php }
} ?>
<br>
<div class="FindUser"><br>
<p style = "text-align:center;font-size:24">Find user based on First Name</p>

<form method="post">
  <label style = "margin:10px;" for="exampleFormControlInput1">First Name:</label><br>
  <input type="text" id="firstname" class="TextBox" name="firstname"><br><br>
  <center><input type="submit" class ="WebButton" name="submit" id = "submit" value="View Results"><center>
</form>

</div>
</body>
</html>