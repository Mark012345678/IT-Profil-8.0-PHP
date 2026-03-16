<?php
session_start();

require_once 'init.php';

// načtení profilu z databáze
$stmt = $db->query("SELECT * FROM profile WHERE id = 1");
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
$name = $profile ? $profile['name'] : 'Neznámý uživatel';
$skills = $profile ? json_decode($profile['skills_json'], true) : [];

// načtení zájmů z databáze
$stmt = $db->query("SELECT * FROM interests");
$interests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// načtení projektů z databáze
$stmt = $db->query("SELECT * FROM projects");
$real_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// připravíme proměnné pro notifikace ze session
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$messageType = isset($_SESSION['messageType']) ? $_SESSION['messageType'] : '';
unset($_SESSION['message'], $_SESSION['messageType']);

// zpracování formulářů pro přidání, odstranění nebo editaci zájmu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // přidání zájmu
    if (isset($_POST['new_interest'])) {
        $new = trim($_POST['new_interest']);
        if ($new === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO interests (name) VALUES (?)");
                $stmt->execute([$new]);
                $_SESSION['message'] = 'Zájem byl přidán.';
                $_SESSION['messageType'] = 'success';
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // UNIQUE constraint failed
                    $_SESSION['message'] = 'Tento zájem už existuje.';
                    $_SESSION['messageType'] = 'error';
                } else {
                    $_SESSION['message'] = 'Chyba při přidávání zájmu.';
                    $_SESSION['messageType'] = 'error';
                }
            }
        }
        header("Location: index.php");
        exit;
    }
    // odstranění zájmu
    elseif (isset($_POST['remove_interest'])) {
        $id = (int)$_POST['remove_interest'];
        $stmt = $db->prepare("DELETE FROM interests WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = 'Zájem byl odstraněn.';
            $_SESSION['messageType'] = 'success';
        } else {
            $_SESSION['message'] = 'Zájem nebyl nalezen.';
            $_SESSION['messageType'] = 'error';
        }
        header("Location: index.php");
        exit;
    }
    // zahájení editace zájmu
    elseif (isset($_POST['edit_interest'])) {
        $id = (int)$_POST['edit_interest'];
        $_SESSION['editing_interest'] = $id;
        header("Location: index.php");
        exit;
    }
    // uložení editace zájmu
    elseif (isset($_POST['save_edit_interest'])) {
        $id = (int)$_POST['save_edit_interest'];
        $new_value = trim($_POST['edited_interest']);
        if ($new_value === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } else {
            try {
                $stmt = $db->prepare("UPDATE interests SET name = ? WHERE id = ?");
                $stmt->execute([$new_value, $id]);
                if ($stmt->rowCount() > 0) {
                    $_SESSION['message'] = 'Zájem byl upraven.';
                    $_SESSION['messageType'] = 'success';
                    unset($_SESSION['editing_interest']);
                } else {
                    $_SESSION['message'] = 'Zájem nebyl nalezen.';
                    $_SESSION['messageType'] = 'error';
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // UNIQUE constraint failed
                    $_SESSION['message'] = 'Tento zájem už existuje.';
                    $_SESSION['messageType'] = 'error';
                } else {
                    $_SESSION['message'] = 'Chyba při úpravě zájmu.';
                    $_SESSION['messageType'] = 'error';
                }
            }
        }
        header("Location: index.php");
        exit;
    }
    // zrušení editace zájmu
    elseif (isset($_POST['cancel_edit_interest'])) {
        unset($_SESSION['editing_interest']);
        header("Location: index.php");
        exit;
    }
    // přidání projektu
    elseif (isset($_POST['new_project'])) {
        $new = trim($_POST['new_project']);
        if ($new === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO projects (name) VALUES (?)");
                $stmt->execute([$new]);
                $_SESSION['message'] = 'Projekt byl přidán.';
                $_SESSION['messageType'] = 'success';
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // UNIQUE constraint failed
                    $_SESSION['message'] = 'Tento projekt už existuje.';
                    $_SESSION['messageType'] = 'error';
                } else {
                    $_SESSION['message'] = 'Chyba při přidávání projektu.';
                    $_SESSION['messageType'] = 'error';
                }
            }
        }
        header("Location: index.php");
        exit;
    }
    // odstranění projektu
    elseif (isset($_POST['remove_project'])) {
        $id = (int)$_POST['remove_project'];
        $stmt = $db->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = 'Projekt byl odstraněn.';
            $_SESSION['messageType'] = 'success';
        } else {
            $_SESSION['message'] = 'Projekt nebyl nalezen.';
            $_SESSION['messageType'] = 'error';
        }
        header("Location: index.php");
        exit;
    }
    // zahájení editace projektu
    elseif (isset($_POST['edit_project'])) {
        $id = (int)$_POST['edit_project'];
        $_SESSION['editing_project'] = $id;
        header("Location: index.php");
        exit;
    }
    // uložení editace projektu
    elseif (isset($_POST['save_edit_project'])) {
        $id = (int)$_POST['save_edit_project'];
        $new_value = trim($_POST['edited_project']);
        if ($new_value === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } else {
            try {
                $stmt = $db->prepare("UPDATE projects SET name = ? WHERE id = ?");
                $stmt->execute([$new_value, $id]);
                if ($stmt->rowCount() > 0) {
                    $_SESSION['message'] = 'Projekt byl upraven.';
                    $_SESSION['messageType'] = 'success';
                    unset($_SESSION['editing_project']);
                } else {
                    $_SESSION['message'] = 'Projekt nebyl nalezen.';
                    $_SESSION['messageType'] = 'error';
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // UNIQUE constraint failed
                    $_SESSION['message'] = 'Tento projekt už existuje.';
                    $_SESSION['messageType'] = 'error';
                } else {
                    $_SESSION['message'] = 'Chyba při úpravě projektu.';
                    $_SESSION['messageType'] = 'error';
                }
            }
        }
        header("Location: index.php");
        exit;
    }
    // zrušení editace projektu
    elseif (isset($_POST['cancel_edit_project'])) {
        unset($_SESSION['editing_project']);
        header("Location: index.php");
        exit;
    }
}

$editing_interest = isset($_SESSION['editing_interest']) ? $_SESSION['editing_interest'] : null;
$editing_project = isset($_SESSION['editing_project']) ? $_SESSION['editing_project'] : null;

?>

<!doctype html>
<html lang="cs">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>IT Profil - <?php echo htmlspecialchars($name); ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1><?php echo htmlspecialchars($name); ?></h1>
  </header>
  <main>
    <section>
      <h2>Dovednosti</h2>
      <?php if (!empty($skills)): ?>
        <ul>
          <?php foreach ($skills as $skill): ?>
            <li><?php echo htmlspecialchars($skill); ?></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Žádné dovednosti nebyly uvedeny.</p>
      <?php endif; ?>
    </section>
    <section>
      <h2>Zájmy</h2>
      <?php if (!empty($interests)): ?>
        <ul>
          <?php foreach ($interests as $interest): ?>
            <li>
              <?php echo htmlspecialchars($interest['name']); ?>
              <form method="POST" style="display: inline;">
                <input type="hidden" name="edit_interest" value="<?php echo $interest['id']; ?>">
                <button type="submit">Upravit</button>
              </form>
              <form method="POST" style="display: inline;">
                <input type="hidden" name="remove_interest" value="<?php echo $interest['id']; ?>">
                <button type="submit" onclick="return confirm('Opravdu chcete odstranit tento zájem?')">Smazat</button>
              </form>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Žádné projekty nebo zájmy nebyly uvedeny.</p>
      <?php endif; ?>

      <!-- zpráva o výsledku formuláře -->
      <?php if (!empty($message)): ?>
        <p class="<?php echo htmlspecialchars($messageType); ?>">
          <?php echo htmlspecialchars($message); ?>
        </p>
      <?php endif; ?>

      <!-- formulář pro editaci zájmu, pokud je aktivní -->
      <?php if ($editing_interest !== null): ?>
        <?php
        // Najdeme zájem podle id
        $editing_item = null;
        foreach ($interests as $interest) {
          if ($interest['id'] == $editing_interest) {
            $editing_item = $interest;
            break;
          }
        }
        ?>
        <?php if ($editing_item): ?>
          <h3>Upravit zájem</h3>
          <form method="POST">
            <input type="text" name="edited_interest" value="<?php echo htmlspecialchars($editing_item['name']); ?>" required>
            <input type="hidden" name="save_edit_interest" value="<?php echo $editing_item['id']; ?>">
            <button type="submit">Uložit změny</button>
            <button type="submit" name="cancel_edit_interest">Zrušit</button>
          </form>
        <?php endif; ?>
      <?php endif; ?>

      <!-- formulář pro nový zájem -->
      <h3>Přidat nový zájem</h3>
      <form method="POST">
        <input type="text" name="new_interest" required>
        <button type="submit">Přidat zájem</button>
      </form>
    </section>
    <section>
      <h2>Projekty</h2>
      <?php if (!empty($real_projects)): ?>
        <ul>
          <?php foreach ($real_projects as $project): ?>
            <li>
              <?php echo htmlspecialchars($project['name']); ?>
              <form method="POST" style="display: inline;">
                <input type="hidden" name="edit_project" value="<?php echo $project['id']; ?>">
                <button type="submit">Upravit</button>
              </form>
              <form method="POST" style="display: inline;">
                <input type="hidden" name="remove_project" value="<?php echo $project['id']; ?>">
                <button type="submit" onclick="return confirm('Opravdu chcete odstranit tento projekt?')">Smazat</button>
              </form>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Žádné projekty nebyly uvedeny.</p>
      <?php endif; ?>

      <!-- formulář pro editaci projektu, pokud je aktivní -->
      <?php if ($editing_project !== null): ?>
        <?php
        // Najdeme projekt podle id
        $editing_proj = null;
        foreach ($real_projects as $project) {
          if ($project['id'] == $editing_project) {
            $editing_proj = $project;
            break;
          }
        }
        ?>
        <?php if ($editing_proj): ?>
          <h3>Upravit projekt</h3>
          <form method="POST">
            <input type="text" name="edited_project" value="<?php echo htmlspecialchars($editing_proj['name']); ?>" required>
            <input type="hidden" name="save_edit_project" value="<?php echo $editing_proj['id']; ?>">
            <button type="submit">Uložit změny</button>
            <button type="submit" name="cancel_edit_project">Zrušit</button>
          </form>
        <?php endif; ?>
      <?php endif; ?>

      <!-- formulář pro nový projekt -->
      <h3>Přidat nový projekt</h3>
      <form method="POST">
        <input type="text" name="new_project" required>
        <button type="submit">Přidat projekt</button>
      </form>
    </section>
  </main>
</body>
</html>
