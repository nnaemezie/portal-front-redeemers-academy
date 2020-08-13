<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Theme Made By www.w3schools.com - No Copyright -->
  <title>The Reedemer's Academy</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>
<body>
<?php
	include('config/configTraco.php');
	include('database.php');

    $grade = 'Grade 1A';
    $term  = 'Second Term 2018/2019';
?>
        <div class="container">
            <h2>TRACO Result for <?php echo $grade.' '.$term; ?> </h2>
            <div class="table-responsive">            
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Admission #</th>
                            <th>Class</th>
                            <th>Term</th>
                            <th>Subject</th>
                            <th>1st CA</th>
                            <th>2nd CA</th>
                            <th>3rd CA</th>
                            <th>Exam</th>
                            <th>Total</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php

                        function getGrade($total) {
                            if ($total < 40 ) {
                                $total_f = 'F';
                            }elseif ($total < 49) {
                                $total_f = 'P';
                            }elseif ($total < 69) {
                                $total_f = 'C';
                            }elseif ($total <= 100) {
                                $total_f = 'A';
                            }
                            return $total_f;
                        }

                        $sql1 = "SELECT * FROM `student_data` WHERE class = '$grade' ";
                        $result1 = $database->query($sql1);
                        echo 'Total Number of Student in '.$grade.' = '.$result1->num_rows;

                        $sql = "SELECT * FROM `published_result` WHERE class = '$grade' AND term = '$term' ";
                        $result = $database->query($sql);
                        $cnt = 1;
                        while ($value = $result->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?php echo $cnt; ?></td>
                            <td><?php echo $value['admno']; ?></td>
                            <td><?php echo $value['class']; ?></td>
                            <td><?php echo $value['term']; ?></td>
                            <td><?php echo $value['subject']; ?></td>
                            <td><?php echo $value['ca1']; ?></td>
                            <td><?php echo $value['ca2']; ?></td>
                            <td><?php echo $value['ca3']; ?></td>
                            <td><?php echo $value['exam']; ?></td>
                            <td><?php echo $total = (float)($value['ca1']) + (float)($value['ca2']) + (float)($value['ca3']) + (float)($value['exam']) ?></td>
                            <td><?php echo getGrade($total) ?></td>
                        </tr>
                    <?php
                            $cnt ++;
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
</body>
</html>