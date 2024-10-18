<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>CTIO Ephemeris </title>

</head>
<body>

<div class="container">
       <h1>Cerro Tololo Inter-American Observatory Ephemeris </h1>


       <p>Cerro Tololo ephemeris obtained using Skyfield library in Python. It uses up to JPL ephemeris and IERS time tables</p>
       <p>Each date represents the date at the beggining of the night. </br>


        </p>
        <p> Please choose the range of dates you want the ephemeris. UT is Universal Time, LT is Local Time for Chile.</p>

       <form action="" method="post">
           <label for="start_date">Start Date:</label>
           <input type="date" id="start_date" name="start_date" value="<?php echo isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : ''; ?>" required>
           <label for="end_date">End Date:</label>
           <input type="date" id="end_date" name="end_date" value="<?php echo isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : ''; ?>" required>
           <label for="time_format">Time Format:</label>
           <select id="time_format" name="time_format">
               <option value="UT" <?php echo isset($_POST['time_format']) && $_POST['time_format'] === 'UT' ? 'selected' : ''; ?>>UT</option>
               <option value="LT" <?php echo isset($_POST['time_format']) && $_POST['time_format'] === 'LT' ? 'selected' : ''; ?>>LT</option>
           </select>
           <input type="submit" value="View">
       </form>
   </div>


    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $start_date = '';
      if (isset($_POST["start_date"]) && strtotime($_POST["start_date"])) {
          $start_date = $_POST["start_date"];
      }

      $end_date = '';
      if (isset($_POST["end_date"]) && strtotime($_POST["end_date"])) {
          $end_date = $_POST["end_date"];
      }

      $time_format = '';
      switch ($_POST["time_format"]) {
          case 'LT':
          case 'UT':
              $time_format = $_POST["time_format"];
              break;
          default:
              $time_format = '';
              break;
      }

      $csv_filename = '';
      if ($start_date && $time_format) {
          $csv_filename = "ephem_" . date("Y", strtotime($start_date)) . "_" . $time_format . ".csv";
      }


        // Check if the CSV file exists
        if (file_exists($csv_filename)) {
            echo "<h2>Dates from " . date("F j, Y", strtotime($start_date)) . " until " . date("F j, Y", strtotime($end_date)) . "</h2>";

            echo "<div class='csv-table'>";
            echo "<table>";

            // Open and read the CSV file
            if (($handle = fopen($csv_filename, "r")) !== false) {
                // Output the table header
                $header = fgetcsv($handle);
                echo "<tr>";
                foreach ($header as $column) {
                    $highlight = ''; // Initialize the highlight variable
                    if (strpos($column, 'twi_eve-10_') === 0 || strpos($column, 'twi_mor-10_') === 0) {
                        // Check if the column name starts with "twi_eve-10_" or "twi_mor-10_"
                        $highlight = 'class="highlight"'; // Add the highlight class
                    }
                    echo "<th $highlight>$column</th>";
                }
                echo "</tr>";

                // Output the table rows within the specified date range
                while (($data = fgetcsv($handle)) !== false) {
                    $csv_date = $data[0];
                    if ($csv_date >= $start_date && $csv_date <= $end_date) {
                        echo "<tr>";
                        foreach ($data as $index => $cell) {
                            $highlight = ''; // Initialize the highlight variable
                            if (strpos($header[$index], 'twi_eve-10_') === 0 || strpos($header[$index], 'twi_mor-10_') === 0) {
                                // Check if the header column name starts with "twi_eve-10_" or "twi_mor-10_"
                                $highlight = 'class="highlight"'; // Add the highlight class
                            }
                            echo "<td $highlight>$cell</td>";
                        }
                        echo "</tr>";
                    }
                }
                fclose($handle);
            }

            echo "</table>";
            echo "</div>";
        } else {
            echo "<p class='error'>CSV file not found for the selected year and time format.</p>";
        }
    }     else {
        // Add an else block to hide the table when the page is initially loaded
        echo '<div class="csv-table" style="display: none;"></div>';
    }
    ?>
    <p>  TJD = JD -  2440000  is the Truncated Julian Day.  Moon illum and coordinates computed for local midnight. </br>
      Brightness1 and Brightness2 are the estimated brightness given by the moon in the first and second half of the night,
      respectively. The letter means: D for dark, G for gray and B for bight.
    </p>
<p> Be aware that the tables were generated in October 2023. When selecting local time LT, any changes in Daylight Saving Time for Chile
  after this date won't be reflected in the shown table.
</p>
</body>
</html>
