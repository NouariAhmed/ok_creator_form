<?php 
session_start();
include('secure.php');
include('header.php');
include('../connect.php');
$result = mysqli_query($conn, "SELECT COUNT(*) AS author_count FROM authors");
$row = mysqli_fetch_assoc($result);
$authorCount = $row['author_count'];

$result_book_types = mysqli_query($conn, "SELECT COUNT(*) AS book_type_count FROM book_types");
$row = mysqli_fetch_assoc($result_book_types);
$bookTypeCount = $row['book_type_count'];

$result_book_levels = mysqli_query($conn, "SELECT COUNT(*) AS book_level_count FROM book_levels");
$row = mysqli_fetch_assoc($result_book_levels);
$bookLevelCount = $row['book_level_count'];

$result_subjects = mysqli_query($conn, "SELECT COUNT(*) AS subject_count FROM subjects");
$row = mysqli_fetch_assoc($result_subjects);
$subjectCount = $row['subject_count'];

// Check if the welcome message should be shown
$showWelcomeMessage = false;
if (isset($_SESSION['username']) && isset($_SESSION['showWelcomeMessage']) && $_SESSION['showWelcomeMessage']) {
    $showWelcomeMessage = true;
    // Unset the flag to prevent showing the message on subsequent page loads
    $_SESSION['showWelcomeMessage'] = false;
}
$result = mysqli_query($conn, "
    SELECT *
    FROM authors
    ORDER BY id DESC
    LIMIT 5
");
$items = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_close($conn);
?>
      <div class="container-fluid py-4">
      <div class="row">
<!-- Display welcome message if user is logged in -->
<?php if ($showWelcomeMessage) { ?>

       <div class="col-12 mb-4">
          <div class="alert alert-secondary text-center text-white">
          مرحبًا <?php echo $_SESSION['username']; ?>، أهلاً بك في لوحة التحكم عمل موفق &#x1F60A;
          </div>
        </div>
        <?php 
  } 
  ?>
        <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">person_add</i>
              </div>
              <div class="text-start pt-1">
                <p class="text-sm mb-0 text-capitalize">المؤلفون</p>
                <h4 class="mb-0"><?php echo $authorCount; ?></h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">menu_book</i>
              </div>
              <div class="text-start pt-1">
                <p class="text-sm mb-0 text-capitalize">أنواع الكتب</p>
                <h4 class="mb-0"><?php echo $bookTypeCount; ?></h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
             
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">layers</i>
              </div>
              <div class="text-start pt-1">
                <p class="text-sm mb-0 text-capitalize">المستويات</p>
                <h4 class="mb-0">
                  <span class="text-danger text-sm font-weight-bolder ms-1"></span>
                  <?php echo $bookLevelCount; ?>
                </h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
            
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">subject</i>
              </div>
              <div class="text-start pt-1">
                <p class="text-sm mb-0 text-capitalize">المواد</p>
                <h4 class="mb-0"><?php echo $subjectCount; ?></h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
          <div class="card z-index-2 ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0 ">عدد المؤلفين حسب النوع</h6>
              <p class="text-sm ">آخر الإحصائيات</p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto ms-1">schedule</i>
                <p class="mb-0 text-sm"> سنة 2023 </p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
          <div class="card z-index-2  ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-line" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0 "> عدد المؤلفين - أدمن </h6>
              <p class="text-sm "> عدد المؤلفين لكل أدمن </p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto ms-1">schedule</i>
                <p class="mb-0 text-sm"> سنة 2023 </p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 mt-4 mb-3">
          <div class="card z-index-2 ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-line-tasks" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0 "> عدد المؤلفين - شهر </h6>
              <p class="text-sm "> عدد المؤلفين المضافين في كل شهر </p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm"> سنة 2023 </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row my-4">
        <div class="col-12 mb-md-0 mb-4">
          <div class="card">
            <div class="card-header pb-0">
              <div class="row mb-3">
                <div class="col-6">
                  <h6>المؤلفون</h6>
                  <p class="text-sm font-weight-bold">
                  <i class="fa fa-check text-success" aria-hidden="true"></i>
                   آخر خمسة مؤلفين تم إضافتهم
                  </p>
                </div>
              </div>
            </div>
            <div class="card-body p-0 pb-2">
              <div class="table-responsive">
                <table class="table table-hover align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7">المعرف</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">المؤلف</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">عنوان الكتاب</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">من طرف</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                foreach ($items as $item) {
                ?>
                    <tr>
                      <td class="align-middle text-sm">
                       <h6 class="mb-0 text-sm pe-4"><?php echo htmlspecialchars($item["id"]);?></h6>
                      </td>
                      <td>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($item["authorfullname"]);?></h6>
                            <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($item["phone"]);?></p>
                            <!-- </div> -->
                          </div>
                      </td>
                      <td class="align-middle text-sm">
                        <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($item["book_title"]);?></h6>
                      </td>
                      <td class="align-middle text-sm">
                        <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($item["inserted_by_username"]);?></h6>
                        <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($item["created_at"]);?></p>
                      </td>
                    </tr>
                    <?php
                }
                ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <!--
        <div class="col-lg-4 col-md-6">
          <div class="card h-100">
            <div class="card-header pb-0">
              <h6>نظرة عامة على الطلبات</h6>
              <p class="text-sm">
                <i class="fa fa-arrow-up text-success" aria-hidden="true"></i>
                <span class="font-weight-bold">24%</span> هذا الشهر
              </p>
            </div>
            <div class="card-body p-3">
              <div class="timeline timeline-one-side">
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="material-icons text-success text-gradient">notifications</i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">$2400, تغييرات في التصميم</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">22 DEC 7:20 PM</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="material-icons text-danger text-gradient">code</i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">طلب جديد #1832412</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">21 DEC 11 PM</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="material-icons text-info text-gradient">shopping_cart</i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">مدفوعات الخادم لشهر أبريل</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">21 DEC 9:34 PM</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="material-icons text-warning text-gradient">credit_card</i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">تمت إضافة بطاقة جديدة للطلب #4395133</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">20 DEC 2:20 AM</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="material-icons text-primary text-gradient">key</i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">فتح الحزم من أجل التطوير</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">18 DEC 4:54 AM</p>
                  </div>
                </div>
                <div class="timeline-block">
                  <span class="timeline-step">
                    <i class="material-icons text-dark text-gradient">payments</i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">طلب جديد #9583120</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">17 DEC</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
-->
      </div>

<?php
include('footer.php');
?>