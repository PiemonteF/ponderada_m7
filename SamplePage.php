<?php include "../inc/dbinfo.inc"; ?>
<html>
<body>
<h1>Sample page</h1>
<?php
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  VerifyBooksTable($connection, DB_DATABASE);

  $book_title = htmlentities($_POST['TITLE']);
  $book_author = htmlentities($_POST['AUTHOR']);
  $book_published_year = htmlentities($_POST['PUBLISHED_YEAR']);

  if (strlen($book_title) || strlen($book_author) || strlen($book_published_year)) {
    AddBook($connection, $book_title, $book_author, $book_published_year);
  }
?>

<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>TITLE</td>
      <td>AUTHOR</td>
      <td>PUBLISHED_YEAR</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="TITLE" maxlength="100" size="30" />
      </td>
      <td>
        <input type="text" name="AUTHOR" maxlength="100" size="30" />
      </td>
      <td>
        <input type="number" name="PUBLISHED_YEAR" min="1000" max="9999" />
      </td>
      <td>
        <input type="submit" value="Add Data" />
      </td>
    </tr>
  </table>
</form>

<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>TITLE</td>
    <td>AUTHOR</td>
    <td>PUBLISHED_YEAR</td>
  </tr>

<?php
$result = mysqli_query($connection, "SELECT * FROM BOOKS");

while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>",$query_data[0], "</td>",
       "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$query_data[3], "</td>";
  echo "</tr>";
}
?>

</table>

<?php
  mysqli_free_result($result);
  mysqli_close($connection);
?>

</body>
</html>

<?php
function AddBook($connection, $title, $author, $published_year) {
   $t = mysqli_real_escape_string($connection, $title);
   $a = mysqli_real_escape_string($connection, $author);
   $p = mysqli_real_escape_string($connection, $published_year);

   $query = "INSERT INTO BOOKS (TITLE, AUTHOR, PUBLISHED_YEAR) VALUES ('$t', '$a', '$p');";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding book data.</p>");
}

function VerifyBooksTable($connection, $dbName) {
  if(!TableExists("BOOKS", $connection, $dbName))
  {
     $query = "CREATE TABLE BOOKS (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         TITLE VARCHAR(100),
         AUTHOR VARCHAR(100),
         PUBLISHED_YEAR INT(4)
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
}

function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>