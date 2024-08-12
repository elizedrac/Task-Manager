<?php
  include "task_connect.php";
  $db = connect();

  $serviceDate = $db->query("SELECT * FROM currDate");
  $currDate = $serviceDate->fetch(PDO::FETCH_ASSOC);
  
  date_default_timezone_set('Europe/Athens');
  $day = date("d");
  $month = date("m");

  $change = FALSE;
  $idEdit = 0;
?>

<!-- html code -->
<!doctype html>
<html lang="en" class="h-100">
  <head>
  	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task Manager</title>

    <div id = "datePicker" style = "border: 2px solid black;background-color: lightsalmon; padding: 10px; width: 300px"><center>
      <h3>Select Day</h3>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
      <input type="date" name="today" value="<?php echo $date;?>" style = "padding: 3px;border: 1px solid black;">
      <input type="submit" name="date_submit" style = "border-radius: 10px; background-color: floralwhite; padding: 5px; border: 2px solid black;">
      </form>
      <?php if(isset($_POST['date_submit'])){
        $date = $_POST['today'];
        $db->query("UPDATE currDate SET currDate = '$date' WHERE id = 1");

        $day = date('d', strtotime($_POST['today']));
        $month = date('m', strtotime($_POST['today']));
      } 

      $dateQuery = $db->query("SELECT * FROM currDate WHERE id = 1");
      $date = $dateQuery->fetch(PDO::FETCH_ASSOC);
      $date = date("m/d/y", strtotime($date['currDate']));
      ?>
      <h4>Current Selected Date: <?=$date?> </h4>
    </div>


    <br></br><center><h1>Task Manager</h1>


  </head>



  <center><br></br><div style= "border: 5px solid black; width: 500px; background-color: floralwhite;">
  <h2>Input Task</h2>

  <?php
  //handles submit button
  if (isset($_POST['submit'])) {
    //if editing vs submitting
    if ($_POST['change'] == 'true'){
      $id = $_POST['id'];

      if(!empty($_POST['description'])) {
        $description = $_POST['description'];
        $test = $db->prepare("UPDATE tasks SET description = :description WHERE id = :id");
        $test->bindValue(':description', $description, PDO::PARAM_STR); //ensures correct parameter
        $test->bindValue(':id', $id, PDO::PARAM_INT); //ensures correct parameter
        $test->execute();

      }

      if(!empty($_POST['price'])) {
        $description = $_POST['price'];
        $test = $db->prepare("UPDATE tasks SET price = :description WHERE id = :id");
        $test->bindValue(':description', $description, PDO::PARAM_INT); //ensures correct parameter
        $test->bindValue(':id', $id, PDO::PARAM_INT); //ensures correct parameter
        $test->execute();
      }

      if(!empty($_POST['company'])) {
        $description = $_POST['company'];
        $test = $db->prepare("UPDATE tasks SET company = :description WHERE id = :id");
        $test->bindValue(':description', $description, PDO::PARAM_STR); //ensures correct parameter
        $test->bindValue(':id', $id, PDO::PARAM_INT); //ensures correct parameter
        $test->execute();
      }

      if(!empty($_POST['time'])) {
        $description = $_POST['time'];
        $test = $db->prepare("UPDATE tasks SET currTime = '$description' WHERE id = :id");
        $test->bindValue(':id', $id, PDO::PARAM_INT); //ensures correct parameter
        $test->execute();
      }

      
    } else {
      //check if all values are complete first (also if int is an int)
      if(!empty($_POST['description']) && !empty($_POST['price']) && !empty($_POST['company']) && !empty($_POST['time'])) {
        $description = $_POST['description'];
        $price = $_POST['price'];
        $company = $_POST['company'];

        $time = $_POST['time'];
        $date = $currDate['currDate'];

        $test = $db->prepare("INSERT INTO tasks (description, price, company, completed, currTime, currDate) VALUES (:description,:price, :company, FALSE, '$time', '$date')");
        $test->bindValue(':description', $description, PDO::PARAM_STR); //ensures correct parameter
        $test->bindValue(':price', $price, PDO::PARAM_INT); //ensures correct parameter
        $test->bindValue(':company', $company, PDO::PARAM_STR); //ensures correct parameter
        $test->execute();

      } else {
        ?>

        <center><div style = "color: salmon">Please fill out all fields before submitting <br></br></div>
        <?php
      }
    }

  } elseif (isset($_POST['delete'])){ 
    $id = $_POST['id'];
    $test = $db->prepare("DELETE FROM tasks WHERE id = :id");
    $test->bindValue(':id', $id, PDO::PARAM_INT); //ensures correct parameter
    $test->execute();

  } elseif (isset($_POST['complete'])){ 
    $id = $_POST['id'];
    $val = $_POST['val'];
    $test = $db->prepare("UPDATE tasks SET completed = !$val WHERE id = :id");
    $test->bindValue(':id', $id, PDO::PARAM_INT); //ensures correct parameter
    $test->execute();

  } elseif (isset($_POST['edit'])){ 
    $idEdit = $_POST['id'];
    ?> 

    <center><div style = "color: salmon">Fill out fields you want to change<br></br></div>



    <?php
    $change = TRUE;

  }
  ?>



<!-- <form action="/action_page.php">
  <label for="appt">Select a time:</label>
  <input type="time" id="appt" name="appt">
  <input type="submit">
</form> -->

  <form method="post"; action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div class= "form_input">
      <input type="text" placeholder = "Task Description" name="description" style = "padding: 10px;border: 1px solid black;">
      <br></br>
    </div> 
    <div class= "form_input">
      <input type="text" placeholder = "Price" name="price" style = "padding: 10px;border: 1px solid black;">
      <br></br>
    </div>  
    <div class= "form_input">
      <input type="text" placeholder = "Company" name="company" style = "padding: 10px;border: 1px solid black;">
      <br></br>
    </div>  
    <div class= "form_input">
      <input type="time" name="time" class="<?php echo date("h:i");?>" style = "padding: 3px;border: 1px solid black;">
      <br></br>
    </div>  
    <div class= "form_input">
      <input type="submit" name="submit" value="Submit" style = "border-radius: 10px; background-color: lightsalmon; padding: 5px; border: 2px solid black;">

      <?php $val = $change ? 'true' : 'false';?>       
      <input type="hidden" name="change" value="<?=$val?>">
      <input type="hidden" name="id" value="<?=$idEdit?>">
      <br></br>
    </div> 
  </form>
</div></center>
  
</body>


<main>
  <center>
    <br></br>
  <table style="border-collapse: collapse;">
  <tr>
      <th style="padding-left: 25px; padding-right: 25px; border: 2px black solid; background-color: floralwhite;">Task Description</th>
      <th style="padding-left: 25px; padding-right: 25px; border: 2px black solid; background-color: floralwhite;">Price</th>
      <th style="padding-left: 25px; padding-right: 25px; border: 2px black solid; background-color: floralwhite;">Start Time</th>
      <th style="padding-left: 25px; padding-right: 25px; border: 2px black solid; background-color: floralwhite;">Company</th>
      <th style="padding-left: 25px; padding-right: 25px;border: 2px black solid; background-color: floralwhite;">Status</th>
       <th style="padding-left: 25px; padding-right: 25px;border: 2px black solid; background-color: floralwhite;">Actions</th>
  </tr>

<!-- only run once -->
  <?php
    $taskQuery = $db->query("SELECT * FROM tasks ORDER BY currTime ASC");
    $results = $taskQuery->fetchAll(PDO::FETCH_ASSOC);

    $dateQuery = $db->query("SELECT * FROM currDate WHERE id = 1");
    $date = $dateQuery->fetch(PDO::FETCH_ASSOC);

    foreach ($results as $result) {
      $val = '';

      if ($result['completed']) {
        $val = 'True';
      } else {
        $val = 'False';
      }

      if ($date['currDate'] == $result['currDate']) {
      ?>
    
      <tr>
      <td style = "border: 2px solid black; padding-left: 10px; padding-right: 10px;">
      <?php
        echo $result['description'] . "</td>";

        ?>
      

        <td style = "border: 2px solid black; padding-left: 10px; padding-right: 10px;">
        <?php
        echo $result['price'] . "</td>";
        ?>

        <td style = "border: 2px solid black; padding-left: 10px; padding-right: 10px;">
        <?php
        echo date('h:i', strtotime($result['currTime'])) . "</td>";
        ?>

        <td style = "border: 2px solid black; padding-left: 10px; padding-right: 10px;">
        <?php
        echo $result['company'] . "</td>";
        ?>

        <?php
          $completed = '';
          if ($result['completed']) {
            $completed = 'Mark as Incomplete';
            ?>
            <td style = "background-color: lightseagreen; border: 2px solid black; padding-left: 10px; padding-right: 10px;">
            <?php
            echo "Complete";
          } else {
            $completed = 'Mark as Complete';
            ?>
            <td style = "background-color: lightcoral; border: 2px solid black; padding-left: 10px; padding-right: 10px;">
            <?php
            echo "Incomplete";
          }
        ?>

        
        <!-- completed button next to each task -->
        <td style = "border: 2px solid black; padding-left: 10px; padding-right: 10px;">
        <form method="POST">
        <button type="submit" name="complete"><?=$completed?></button>
        <input type="hidden" name="id" value="<?=$result['id']?>">
        <input type="hidden" name="val" value="<?=$result['completed']?>">
        <input type="hidden" name="complete" value="true">
        </form>


        <!-- delete button next to each task -->
        <form method="POST">
        <button type="submit" name="delete">Delete</button>
        <input type="hidden" name="id" value="<?=$result['id']?>">
        <input type="hidden" name="delete" value="true">
        </form>

        <!-- delete button next to each task -->
        <form method="POST">
        <button type="submit" name="edit">Edit</button>
        <input type="hidden" name="id" value="<?=$result['id']?>">
        <input type="hidden" name="edit" value="true">
        </form></td>

      <?php
      echo "<br>\n";
      echo "</tr>";
    }
    }
  ?>
</table>

<br></br>
<br></br>
<br></br>
</main>




