
<?php


function write_login_log($user_login, $user) {
    $log_file = plugin_dir_path(__FILE__) . 'logs/user_login.log'; // Chemin du fichier de log
    $user_id = $user->ID;
    $user_ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $current_time = current_time('mysql');

    $log_entry = sprintf(
        "[%s] User: %s (ID: %d) IP: %s User Agent: %s\n",
        $current_time,
        $user_login,
        $user_id,
        $user_ip,
        $user_agent
    );

    // Ouvrir le fichier en mode append (ajout)
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}


// Créer le dossier de logs s'il n'existe pas
$log_dir = plugin_dir_path(__FILE__) . 'logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0755, true);
}



function create_ia_seure_wordpress_histories_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ia_seure_wordpress_histories';

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $charset_collate = $wpdb->get_charset_collate();

    // SQL pour créer la table si elle n'existe pas
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED DEFAULT NULL,
        user_login VARCHAR(60) NOT NULL,
        user_ip VARCHAR(45) NOT NULL,
        user_agent TEXT NOT NULL,
        user_date DATETIME NOT NULL,
        action VARCHAR(20) NOT NULL,
        failed_attempts INT(11) DEFAULT 0,
        lock_until DATETIME DEFAULT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    dbDelta($sql);
}

// Attacher la création de la table à l'activation du plugin
register_activation_hook(__FILE__, 'create_ia_seure_wordpress_histories_table');

// 2. Enregistrer les connexions réussies
function record_user_login($user_login, $user) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ia_seure_wordpress_histories';
    $user_id = $user->ID;
    $user_ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $current_time = current_time('mysql');

    // Enregistrer la connexion réussie
    $wpdb->insert(
        $table_name,
        array(
            'user_id'        => $user_id,
            'user_login'     => $user_login,
            'user_ip'        => $user_ip,
            'user_agent'     => $user_agent,
            'user_date'      => $current_time,
            'action'         => 'LOGIN',
            'failed_attempts'=> 0,
            'lock_until'     => NULL
        )
    );

    // Écrire dans le fichier de log
	
    write_login_log($user_login, $user);
}

// Attacher la fonction d'enregistrement de connexion réussie
add_action('wp_login', 'record_user_login', 10, 2);

// 3. Enregistrer les connexions échouées
function record_failed_login($username) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ia_seure_wordpress_histories';
    $user_ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $current_time = current_time('mysql');

    // Récupérer la dernière tentative échouée pour cette IP
    $user = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_ip = %s ORDER BY user_date DESC LIMIT 1",
        $user_ip
    ));

    // Incrémenter le nombre de tentatives échouées
    $failed_attempts = $user ? $user->failed_attempts + 1 : 1;
    $lock_until = null;

    // Bloquer l'utilisateur après 3 tentatives échouées
    if ($failed_attempts >= 3) {
        $lock_until = date('Y-m-d H:i:s', strtotime($current_time . ' +1 hour'));
    }

    // Enregistrer la tentative échouée
    $wpdb->insert(
        $table_name,
        array(
            'user_login'     => $username,
            'user_ip'        => $user_ip,
            'user_agent'     => $user_agent,
            'user_date'      => $current_time,
            'action'         => 'FAILED_LOGIN',
            'failed_attempts'=> $failed_attempts,
            'lock_until'     => $lock_until
        )
    );
}

// Attacher la fonction d'enregistrement de tentative échouée
add_action('wp_login_failed', 'record_failed_login');

// 4. Afficher les historiques de connexion dans l'administration WordPress
function security_historique_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . "ia_seure_wordpress_histories";

    // Récupérer les données de la table
    $user_filter = isset($_GET['user_filter']) ? sanitize_text_field($_GET['user_filter']) : '';
    $where_clause = '';
    if (!empty($user_filter)) {
        $where_clause = $wpdb->prepare("WHERE user_login LIKE '%%%s%%'", $user_filter);
    }

    $query = "SELECT user_login, user_ip, user_agent, user_date, action, failed_attempts 
              FROM $table_name
              $where_clause
              ORDER BY user_date DESC";
    $user_histories = $wpdb->get_results($query);

    ?>
    <div class="wrap">
        <h2>Journal d'activité des utilisateurs</h2>
        <p>Cette page permet de consulter le journal d'activité des utilisateurs.</p>

        <!-- Formulaire de filtre -->
        <form method="get">
            <input type="text" name="user_filter" placeholder="Filtrer par nom" value="<?php echo esc_attr($user_filter); ?>">
            <input type="submit" class="button" value="Filtrer">
        </form>

        <!-- Table des connexions -->
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Date</th>
                    <th>Action</th>
                    <th>Adresse IP</th>
                    <th>Agent utilisateur</th>
                    <th>Tentatives échouées</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($user_histories) {
                    foreach ($user_histories as $history) : ?>
                        <tr>
                            <td><?php echo $history->user_login; ?></td>
                            <td><?php echo $history->user_date; ?></td>
                            <td><?php echo $history->action; ?></td>
                            <td><?php echo $history->user_ip; ?></td>
                            <td><span title="<?php echo esc_attr($history->user_agent); ?>"><?php echo substr($history->user_agent, 0, 25) . '...'; ?></span></td>
                            <td><?php echo $history->failed_attempts; ?></td>
                        </tr>
                    <?php endforeach;
                } else { ?>
                    <tr><td colspan="6">Aucune donnée trouvée.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
}
