<?php

/**
 * Traitement de la valeur du slug envoyé par le formulaire.
 */
function handle_new_admin_slug() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Vérifiez si l'input 'new_admin_slug' est défini dans $_POST
        if (isset($_POST['new_admin_slug'])) {
            // Récupérez la valeur de l'input
            $new_admin_slug = sanitize_text_field($_POST['new_admin_slug']);
			save_custom_admin_slug($new_admin_slug);
            
        } else {
            echo 'Aucun slug n\'a été fourni. <br>';
        }
    }
}


	


function __construct($plugin_name, $version)
{

	$this->plugin_name = $plugin_name;
	$this->version = $version;
}

/**
 * Register the stylesheets for the admin area.
 *
 * @since    1.0.0
 */
 function enqueue_styles()
{

	/**
	 * This function is provided for demonstration purposes only.
	 *
	 * An instance of this class should be passed to the run() function
	 * defined in Ia_Seure_Wordpress_Loader as all of the hooks are defined
	 * in that particular class.
	 *
	 * The Ia_Seure_Wordpress_Loader will then create the relationship
	 * between the defined hooks and the functions defined in this
	 * class.
	 */
	wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/security-ai-admin.css', array(), $this->version, 'all');
}

/**
 * Register the JavaScript for the admin area.
 *
 * @since    1.0.0
 */
 function enqueue_scripts()
{

	/**
	 * This function is provided for demonstration purposes only.
	 *
	 * An instance of this class should be passed to the run() function
	 * defined in Ia_Seure_Wordpress_Loader as all of the hooks are defined
	 * in that particular class.
	 *
	 * The Ia_Seure_Wordpress_Loader will then create the relationship
	 * between the defined hooks and the functions defined in this
	 * class.
	 */
	wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/security-ai-admin.js', array('jquery'), $this->version, false);
}

 function ia_secure_wordpress_identification_page()
{
	global $old_name;
	get_home_path();

	$paths = get_home_path();
	$plugin = $paths  . 'wp-content\plugins\security-ai\security-ai.php';

}
	
	
/**
* Cette fonction vérifie si le fichier .htaccess existe déjà, si c'est le cas, elle vérifie si la règle de réécriture est déjà présente dans le fichier. Si elle ne l'est pas, elle ajoute la règle à la fin du fichier. Si le fichier n'existe pas, elle crée un nouveau fichier .htaccess avec la règle de réécriture. Notez que cette fonction utilise les fonctions file_exists, file_get_contents et file_put_contents pour interagir avec le fichier .htaccess. Assurez-vous que ces fonctions sont autorisées sur votre serveur.
*
* @return void
*/

	
	 function rewrite_rule_htaccess() {
		$htaccess_file = __DIR__ . '/.htaccess'; // Utiliser un chemin absolu
    $rewrite_rule = 'RewriteRule ^(.*)$ $1.php [L,QSA]' . PHP_EOL;

    // Vérifiez si le fichier .htaccess existe et est accessible
    if (file_exists($htaccess_file) && is_writable($htaccess_file)) {
        // Lire le contenu du fichier
        $content = file_get_contents($htaccess_file);

        // Vérifier si la règle est déjà présente
        if (strpos($content, $rewrite_rule) === false) {
            // Ajouter la nouvelle règle
            file_put_contents($htaccess_file, $content . $rewrite_rule);
        }
    } else {
        // Si le fichier n'existe pas ou n'est pas accessible, créer un nouveau fichier avec la règle
        if (is_writable(dirname($htaccess_file))) {
            file_put_contents($htaccess_file, $rewrite_rule);
        } else {
            echo 'Le fichier .htaccess n\'est pas accessible en écriture.';
        }
    }
}
/**
* Crée la table slug_historique avec une valeur initiale de 'wp-login' pour la colonne slug.
*
* @return void
*/
	 function is_plugin_xx_active() {
		include_once( ABSPATH . 'wp-content\plugins\security-ai\security-ai.php' );

		if (is_plugin_active('wp-content\plugins\security-ai\security-ai.php')) 
		{
			
		} else {
			$this->createSlugTable();
		}
	}


/**
* Met à jour la table slug_historique avec une nouvelle valeur de slug.
*
* @param string $new_slug La nouvelle valeur de slug.
* @return void
*/



	 function createSlugTable() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'slug_historique';

		// Requête SQL pour créer la table
		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id INT AUTO_INCREMENT PRIMARY KEY,
			slug VARCHAR(255) NOT NULL DEFAULT 'wp-login',
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		// Vérifie si la table existe déjà
		$table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'");

		if (!$table_exists) {
			// Crée la table
			$result = dbDelta($sql);

			if ($result === false) {
				error_log('Failed to create table slug_historique: ' . $wpdb->last_error);
			} else {
				error_log('Table slug_historique created successfully');

				// Insertion de la donnée par défaut après la création de la table
				$wpdb->insert(
					$table_name,
					array(
						'slug' => 'wp-login',
						'created_at' => current_time('mysql')
					),
					array(
						'%s',
						'%s'
					)
				);

				if ($wpdb->last_error) {
					error_log('Failed to insert default data: ' . $wpdb->last_error);
				} else {
					error_log('Default data inserted successfully');
				}
			}
		} else {
			error_log('Table slug_historique already exists');
		}
	}



	function updateSlug($new_slug) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'slug_historique';

		$wpdb->insert(
			$table_name,
			array(
				'slug' => $new_slug,
				'created_at' => current_time('mysql')
			),
			array(
				'%s',
				'%s'
			)
		);
	}




	 function getLastSlug() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'slug_historique';

		// Requête pour récupérer la dernière ligne insérée
		$last_entry = $wpdb->get_row("
			SELECT * FROM {$table_name}
			ORDER BY id DESC
			LIMIT 1
		");

		// Vérifie si la variable est différente de null
		if ($last_entry !== null) {
			return $last_entry;
		} else {
			return false; // ou un message d'erreur
		}
	}

	 function send_email_to_admins($slug) {
		$paths = get_home_url();
		$admins = get_users(array('role' => 'administrator'));
		foreach ($admins as $admin) {
			$email = $admin->user_email;
			$subject = 'Slug changé';
			$message = 'Le nouveau lien de connexion est : ' .$paths.'/'.$slug;
			wp_mail($email, $subject, $message);
		}
	}


	

	 function save_custom_admin_slug($new_admin_slug) {
		createSlugTable();
		global $wpdb;


			

		
			
			$old_name = getLastSlug();
			
		
			
			
			// Vérifier si un certain plugin est actif
			include_once(ABSPATH . 'wp-admin/includes/plugin.php');

			get_home_path();
			$paths = get_home_path();
			$plugin = $paths  . '/wp-content/plugins/security-ai/security-ai.php';

		
			if (!is_plugin_active($plugin)) {
				$slugs_valide = $new_admin_slug;
	
				// On vérifie si le slug est valide ou non
				if (!preg_match("/^[a-z0-9-]+$/i", $slugs_valide)) {
					echo "Le slug n'est pas valide <br>";
				} else {
					echo "Le slug est valide  <br>";
					
					$slugs_change = $slugs_valide;
					// On change le slug de l'admin
					$chemin_fichier = $paths .  $old_name->slug . '.php';
					
					$mot_a_remplacer = $old_name->slug. '.php';
					
					$nouveau_mot = $slugs_change . '.php';
					

	
					if (!file_exists($chemin_fichier) || !is_readable($chemin_fichier)) {
						return 'Impossible d\'ouvrir le fichier';
					}
	
					// Lire le contenu du fichier
					$contenu_fichier = file_get_contents($chemin_fichier);
	
					// Remplacer chaque occurrence du mot à remplacer par le nouveau mot
					$contenu_fichier_modifie = str_replace($mot_a_remplacer, $nouveau_mot, $contenu_fichier);
	
					// Écrire le contenu modifié dans le fichier
					if (file_put_contents($chemin_fichier, $contenu_fichier_modifie) === FALSE) {
						return 'Impossible d\'écrire dans le fichier';
					}
	
					echo 'Le remplacement a été effectué avec succès.';
					
					//send_email_to_admins($slugs_valide);
			   
					rename("$paths/$old_name->slug.php", "$paths/$nouveau_mot");

					//updateSlug($slugs_valide);
					global $wpdb;
					
					$table_name = $wpdb->prefix . 'slug_historique';
					
					$data = array(
						'slug' => $slugs_valide,
						'created_at' => current_time('mysql')
					);
					
					$format = array(
						'%s',
						'%s'
					);
					
					$wpdb->insert(
						$table_name,
						$data,
						$format
					);
					
					send_email_to_admins($slugs_valide);
	
					rewrite_rule_htaccess();
					$logins = array(
						home_url('wp-login.php', 'relative'),
						home_url('login.php', 'relative'),
						home_url($slugs_valide.'.php', 'relative'),
						site_url($slugs_valide.'.php', 'relative'),
					);
	
					do_action('permalink_structure_changed', $mot_a_remplacer, $slugs_change);
	
					if (in_array(untrailingslashit($_SERVER['REQUEST_URI']), $logins, true)) {
						wp_redirect(wp_login_url());
						exit;
					}

					
				}
			}
		
	}
	


	

	function custom_admin_redirect()
	{
		 // Vérifier si l'URL contient "wp-admin"
		 # Obtenir l'option new_admin_slug
		 $new_admin_slug = get_option('custom_admin_slug');
		 if ( strpos( $new_admin_slug, 'wp-admin' ) !== false ) {
  
			//vérifie si l'utilisateur n'est pas connecté 
			if ( ! is_user_logged_in() ) {
				// Rediriger vers la page 404
				wp_redirect( home_url( '/404' ) );
				exit;
			}
		  }
	}


?>

<!DOCTYPE html>
	<html>
	<head>
		<title>Formulaire d'exemple</title>
	</head>
	<body>
		
			<div class="wrap">
						<h2>Configuration de IA Secure</h2>
						<p>Sur cette page, vous pouvez configurer les paramètres de IA Secure.</p>
			</div>

			<div class="formbold-main-wrapper">

				
					<div class="formbold-form-wrapper">
						<h2>Changement de Login</h2>
						<h4>Veuillez saisir le lien. </h4>
						<p>Exemple : login</p>
						<form method="post" action="">
							<div class="formbold-email-subscription-form">

								<input type="text" id="new_admin_slug" name="new_admin_slug"  class="formbold-form-input" />
								
								<button class="formbold-btn" type="submit" class="button-primary">
									Enregistrer
									<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
										<g clip-path="url(#clip0_1661_1158)">
											<path d="M2.494 0.937761L14.802 7.70709C14.8543 7.73587 14.8978 7.77814 14.9282 7.8295C14.9586 7.88087 14.9746 7.93943 14.9746 7.99909C14.9746 8.05875 14.9586 8.11732 14.9282 8.16868C14.8978 8.22005 14.8543 8.26232 14.802 8.29109L2.494 15.0604C2.44325 15.0883 2.3861 15.1026 2.32818 15.1017C2.27027 15.1008 2.21358 15.0848 2.16372 15.0553C2.11385 15.0258 2.07253 14.9839 2.04382 14.9336C2.01511 14.8833 2.00001 14.8264 2 14.7684V1.22976C2.00001 1.17184 2.01511 1.11492 2.04382 1.06461C2.07253 1.0143 2.11385 0.97234 2.16372 0.942865C2.21358 0.913391 2.27027 0.897419 2.32818 0.896524C2.3861 0.895629 2.44325 0.909842 2.494 0.937761ZM3.33333 8.66576V13.0771L12.5667 7.99909L3.33333 2.92109V7.33243H6.66667V8.66576H3.33333Z" fill="white" />
										</g>
										<defs>
											<clipPath id="clip0_1661_1158">
												<rect width="16" height="16" fill="white" />
											</clipPath>
										</defs>
									</svg>
								</button>
							</div>

						<?php
						// Appeler la fonction pour traiter les données du formulaire
						handle_new_admin_slug();
						?>
					</form>
				<style scop>
					@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

					* {
						margin: 0;
						padding: 0;
						box-sizing: border-box;
					}

					body {
						font-family: "Inter", sans-serif;
					}

					.formbold-main-wrapper {
						display: flex;
						align-items: center;
						justify-content: center;
						padding: 48px;
					}

					.formbold-form-wrapper {
						margin: 0 auto;
						padding: 15px;
						border-radius: 10px;
						max-width: 550px;
						width: 100%;
						background: white;
					}

					.formbold-form-input {
						width: 100%;
						padding: 13px 22px;
						border-radius: 6px;
						border: 1px solid #DDE3EC;
						background: white;
						font-weight: 500;
						font-size: 16px;
						color: #536387;
						outline: none;
						resize: none;
					}

					.formbold-form-input:focus {
						border-color: #6a64f1;
						box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.05);
					}

					.formbold-email-subscription-form {
						display: flex;
						gap: 15px;
					}

					.formbold-btn {
						display: inline-flex;
						align-items: center;
						gap: 8px;
						font-size: 16px;
						border-radius: 5px;
						padding: 13px 25px;
						border: none;
						font-weight: 500;
						background-color: #6A64F1;
						color: white;
						cursor: pointer;
					}

					.formbold-btn:hover {
						box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.05);
					}
				</style>


			</div>
	</div>

	
	</body>
</html>
