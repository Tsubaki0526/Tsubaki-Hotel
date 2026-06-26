<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="index.php?dashboard"><em class="fa fa-home"></em></a></li>
            <li class="active"><?php _e('settings_title'); ?></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-cog"></i> <?php _e('settings_title'); ?>
                    <a href="public/index.php" target="_blank" class="btn btn-sm btn-outline-primary float-end">
                        <i class="fa fa-external-link"></i> <?php _e('settings_view_site'); ?>
                    </a>
                </div>
                <div class="panel-body">
                    <?php
                    $base_url_webhook = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . (filter_var($_SERVER['HTTP_HOST'] ?? '', FILTER_SANITIZE_URL) ?: 'localhost') . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

                    if (isset($_GET['cfg_success'])) echo "<div class='alert alert-success'>" . __('settings_saved') . "</div>";

                    $settings = [];
                    $q = mysqli_query($connection, "SELECT * FROM site_settings");
                    while ($r = mysqli_fetch_assoc($q)) {
                        $settings[$r['key_name']] = $r['key_value'];
                    }
                    ?>
                    <form action="ajax.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                        <!-- ========== EMPRESA ========== -->
                        <div class="section-title">
                            <i class="fa fa-building"></i> <?php _e('settings_section_company'); ?>
                            <small><?php _e('settings_company_info'); ?></small>
                        </div>
                        <div class="settings-section">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _e('settings_hotel_name'); ?></label>
                                        <input type="text" name="site_name" class="form-control" value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _e('settings_tagline'); ?></label>
                                        <input type="text" name="site_tagline" class="form-control" value="<?php echo htmlspecialchars($settings['site_tagline'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php _e('settings_description'); ?></label>
                                <textarea name="site_description" class="form-control" rows="2"><?php echo htmlspecialchars($settings['site_description'] ?? ''); ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fa fa-envelope"></i> <?php _e('settings_contact_email'); ?></label>
                                        <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fa fa-phone"></i> <?php _e('settings_phone'); ?></label>
                                        <input type="text" name="contact_phone" class="form-control" value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fa fa-whatsapp"></i> <?php _e('settings_whatsapp'); ?></label>
                                        <input type="text" name="social_whatsapp" class="form-control" value="<?php echo htmlspecialchars($settings['social_whatsapp'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-map-marker"></i> <?php _e('settings_address'); ?></label>
                                <input type="text" name="contact_address" class="form-control" value="<?php echo htmlspecialchars($settings['contact_address'] ?? ''); ?>">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fa fa-clock-o"></i> <?php _e('settings_hours'); ?></label>
                                        <input type="text" name="business_hours" class="form-control" value="<?php echo htmlspecialchars($settings['business_hours'] ?? 'Lunes a Domingo: 24 horas'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fa fa-map"></i> <?php _e('settings_map_embed'); ?></label>
                                        <input type="text" name="map_embed" class="form-control" value="<?php echo htmlspecialchars($settings['map_embed'] ?? ''); ?>" placeholder="https://www.google.com/maps/embed?pb=...">
                                    </div>
                                </div>
                            </div>

                            <div class="section-subtitle"><i class="fa fa-share-alt"></i> <?php _e('settings_social'); ?></div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fa fa-facebook"></i> <?php _e('settings_facebook'); ?></label>
                                        <input type="url" name="social_facebook" class="form-control" value="<?php echo htmlspecialchars($settings['social_facebook'] ?? '#'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fa fa-instagram"></i> <?php _e('settings_instagram'); ?></label>
                                        <input type="url" name="social_instagram" class="form-control" value="<?php echo htmlspecialchars($settings['social_instagram'] ?? '#'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fa fa-twitter"></i> <?php _e('settings_twitter'); ?></label>
                                        <input type="url" name="social_twitter" class="form-control" value="<?php echo htmlspecialchars($settings['social_twitter'] ?? '#'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fa fa-youtube"></i> <?php _e('settings_youtube'); ?></label>
                                        <input type="url" name="social_youtube" class="form-control" value="<?php echo htmlspecialchars($settings['social_youtube'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========== HERO ========== -->
                        <div class="section-title">
                            <i class="fa fa-home"></i> <?php _e('settings_section_hero'); ?>
                            <small><?php _e('settings_hero_desc'); ?></small>
                        </div>
                        <div class="settings-section">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _e('settings_hero_title'); ?></label>
                                        <input type="text" name="hero_title" class="form-control" value="<?php echo htmlspecialchars($settings['hero_title'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _e('settings_hero_subtitle'); ?></label>
                                        <input type="text" name="hero_subtitle" class="form-control" value="<?php echo htmlspecialchars($settings['hero_subtitle'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php _e('settings_hero_text'); ?></label>
                                <textarea name="hero_text" class="form-control" rows="2"><?php echo htmlspecialchars($settings['hero_text'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label><?php _e('settings_hero_image'); ?></label>
                                <?php if (!empty($settings['hero_image'])): 
                                    $h_img_src = (strpos($settings['hero_image'], 'http') === 0) ? $settings['hero_image'] : '../uploads/' . $settings['hero_image'];
                                ?>
                                    <div class="preview-img">
                                        <img src="<?php echo htmlspecialchars($h_img_src); ?>" alt="Hero preview">
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted" style="margin-bottom:8px;"><?php _e('settings_no_image'); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _e('settings_upload_image'); ?></label>
                                        <input type="file" name="hero_image_file" accept="image/*" class="form-control">
                                        <small><?php _e('settings_image_formats'); ?></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _e('settings_or_url'); ?></label>
                                        <input type="url" name="hero_image_url" class="form-control" value="<?php echo htmlspecialchars((strpos($settings['hero_image'] ?? '', 'http') === 0) ? $settings['hero_image'] : ''); ?>" placeholder="https://ejemplo.com/imagen.jpg">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========== NOSOTROS ========== -->
                        <div class="section-title">
                            <i class="fa fa-info-circle"></i> <?php _e('settings_section_about'); ?>
                            <small><?php _e('settings_about_desc'); ?></small>
                        </div>
                        <div class="settings-section">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label><?php _e('settings_about_title'); ?></label>
                                        <input type="text" name="about_title" class="form-control" value="<?php echo htmlspecialchars($settings['about_title'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label><?php _e('settings_about_text'); ?></label>
                                        <textarea name="about_text" class="form-control" rows="4"><?php echo htmlspecialchars($settings['about_text'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php _e('settings_about_image'); ?></label>
                                        <?php if (!empty($settings['about_image'])): 
                                            $img_src = (strpos($settings['about_image'], 'http') === 0) ? $settings['about_image'] : '../uploads/' . $settings['about_image'];
                                        ?>
                                            <div class="preview-img">
                                                <img src="<?php echo htmlspecialchars($img_src); ?>" alt="About preview">
                                            </div>
                                        <?php else: ?>
<div class="text-muted" style="margin-bottom:8px;"><?php _e('settings_no_image'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label><?php _e('settings_upload_new'); ?></label>
                                        <input type="file" name="about_image_file" accept="image/*" class="form-control">
                                        <small><?php _e('settings_image_formats'); ?></small>
                                    </div>
                                    <div class="form-group">
                                        <label><?php _e('settings_or_url'); ?></label>
                                        <input type="url" name="about_image_url" class="form-control" value="<?php echo htmlspecialchars((strpos($settings['about_image'] ?? '', 'http') === 0) ? $settings['about_image'] : ''); ?>" placeholder="https://ejemplo.com/imagen.jpg">
                                        <small><?php _e('settings_url_overrides'); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========== CHECK IN / FOOTER ========== -->
                        <div class="section-title">
                            <i class="fa fa-gears"></i> <?php _e('settings_section_extra'); ?>
                            <small>Check-in, check-out, footer</small>
                        </div>
                        <div class="settings-section">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _e('settings_checkin_time'); ?></label>
                                        <input type="text" name="check_in_time" class="form-control" value="<?php echo htmlspecialchars($settings['check_in_time'] ?? '2:00 PM'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _e('settings_checkout_time'); ?></label>
                                        <input type="text" name="check_out_time" class="form-control" value="<?php echo htmlspecialchars($settings['check_out_time'] ?? '12:00 PM'); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php _e('settings_footer_text'); ?></label>
                                <textarea name="footer_about" class="form-control" rows="2"><?php echo htmlspecialchars($settings['footer_about'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label><?php _e('settings_copyright'); ?></label>
                                <input type="text" name="copyright_text" class="form-control" value="<?php echo htmlspecialchars($settings['copyright_text'] ?? '© 2024 Hotel. Todos los derechos reservados.'); ?>">
                            </div>
                        </div>

                        <!-- ========== PASARELA DE PAGO ========== -->
                        <div class="section-title">
                            <i class="fa fa-credit-card"></i> <?php _e('settings_section_payment'); ?>
                            <small><?php _e('settings_payment_desc'); ?></small>
                        </div>
                        <div class="settings-section">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php _e('settings_gateway_active'); ?></label>
                                        <select name="gateway_enabled" class="form-control">
                                            <option value="stripe" <?php echo ($settings['gateway_enabled'] ?? 'stripe') == 'stripe' ? 'selected' : ''; ?>><?php _e('settings_gateway_stripe'); ?></option>
                                            <option value="paypal" <?php echo ($settings['gateway_enabled'] ?? '') == 'paypal' ? 'selected' : ''; ?>><?php _e('settings_gateway_paypal'); ?></option>
                                            <option value="mercadopago" <?php echo ($settings['gateway_enabled'] ?? '') == 'mercadopago' ? 'selected' : ''; ?>><?php _e('settings_gateway_mp'); ?></option>
                                            <option value="both" <?php echo ($settings['gateway_enabled'] ?? '') == 'both' ? 'selected' : ''; ?>><?php _e('settings_gateway_both'); ?></option>
                                            <option value="all" <?php echo ($settings['gateway_enabled'] ?? '') == 'all' ? 'selected' : ''; ?>><?php _e('settings_gateway_all'); ?></option>
                                            <option value="manual" <?php echo ($settings['gateway_enabled'] ?? '') == 'manual' ? 'selected' : ''; ?>><?php _e('settings_gateway_manual'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php _e('settings_currency'); ?></label>
                                        <select name="stripe_currency" class="form-control">
                                            <option value="usd" <?php echo ($settings['stripe_currency'] ?? 'usd') == 'usd' ? 'selected' : ''; ?>>USD - Dólar</option>
                                            <option value="cop" <?php echo ($settings['stripe_currency'] ?? '') == 'cop' ? 'selected' : ''; ?>>COP - Peso Colombiano</option>
                                            <option value="mxn" <?php echo ($settings['stripe_currency'] ?? '') == 'mxn' ? 'selected' : ''; ?>>MXN - Peso Mexicano</option>
                                            <option value="brl" <?php echo ($settings['stripe_currency'] ?? '') == 'brl' ? 'selected' : ''; ?>>BRL - Real Brasileño</option>
                                            <option value="ars" <?php echo ($settings['stripe_currency'] ?? '') == 'ars' ? 'selected' : ''; ?>>ARS - Peso Argentino</option>
                                            <option value="clp" <?php echo ($settings['stripe_currency'] ?? '') == 'clp' ? 'selected' : ''; ?>>CLP - Peso Chileno</option>
                                            <option value="pen" <?php echo ($settings['stripe_currency'] ?? '') == 'pen' ? 'selected' : ''; ?>>PEN - Sol Peruano</option>
                                            <option value="eur" <?php echo ($settings['stripe_currency'] ?? '') == 'eur' ? 'selected' : ''; ?>>EUR - Euro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php _e('settings_bank_country'); ?></label>
                                        <select name="payment_bank_country" class="form-control">
                                            <option value="CO" <?php echo ($settings['payment_bank_country'] ?? 'CO') == 'CO' ? 'selected' : ''; ?>>Colombia</option>
                                            <option value="MX" <?php echo ($settings['payment_bank_country'] ?? '') == 'MX' ? 'selected' : ''; ?>>México</option>
                                            <option value="AR" <?php echo ($settings['payment_bank_country'] ?? '') == 'AR' ? 'selected' : ''; ?>>Argentina</option>
                                            <option value="BR" <?php echo ($settings['payment_bank_country'] ?? '') == 'BR' ? 'selected' : ''; ?>>Brasil</option>
                                            <option value="CL" <?php echo ($settings['payment_bank_country'] ?? '') == 'CL' ? 'selected' : ''; ?>>Chile</option>
                                            <option value="PE" <?php echo ($settings['payment_bank_country'] ?? '') == 'PE' ? 'selected' : ''; ?>>Perú</option>
                                            <option value="EC" <?php echo ($settings['payment_bank_country'] ?? '') == 'EC' ? 'selected' : ''; ?>>Ecuador</option>
                                            <option value="US" <?php echo ($settings['payment_bank_country'] ?? '') == 'US' ? 'selected' : ''; ?>>Estados Unidos</option>
                                            <option value="ES" <?php echo ($settings['payment_bank_country'] ?? '') == 'ES' ? 'selected' : ''; ?>>España</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Stripe -->
                            <div class="section-subtitle"><i class="fa fa-cc-stripe"></i> <?php _e('settings_stripe'); ?></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _e('settings_stripe_publishable'); ?></label>
                                        <input type="text" name="stripe_publishable_key" class="form-control" value="<?php echo htmlspecialchars($settings['stripe_publishable_key'] ?? ''); ?>" placeholder="pk_live_..." style="font-family:monospace;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _e('settings_stripe_secret'); ?></label>
                                        <input type="password" name="stripe_secret_key" class="form-control" value="" placeholder="••••••••••••••••" style="font-family:monospace;">
                                        <small><i class="fa fa-lock"></i> <?php _e('settings_key_hidden'); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php _e('settings_stripe_webhook'); ?></label>
                                        <input type="password" name="stripe_webhook_secret" class="form-control" value="" placeholder="••••••••••••••••" style="font-family:monospace;">
                                        <small><i class="fa fa-lock"></i> <?php _e('settings_key_hidden'); ?></small>
                                <small><?php _e('settings_stripe_webhook_url'); ?>: <code><?php echo $base_url_webhook; ?>/webhook_stripe.php</code> — <?php _e('settings_stripe_events'); ?></small>
                            </div>

                            <!-- PayPal -->
                            <div class="section-subtitle"><i class="fa fa-cc-paypal"></i> <?php _e('settings_paypal'); ?></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _e('settings_paypal_client'); ?></label>
                                        <input type="text" name="paypal_client_id" class="form-control" value="<?php echo htmlspecialchars($settings['paypal_client_id'] ?? ''); ?>" style="font-family:monospace;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _e('settings_paypal_secret'); ?></label>
                                        <input type="password" name="paypal_secret" class="form-control" value="" placeholder="••••••••••••••••" style="font-family:monospace;">
                                        <small><i class="fa fa-lock"></i> <?php _e('settings_key_hidden'); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php _e('settings_paypal_mode'); ?></label>
                                <select name="paypal_mode" class="form-control">
                                    <option value="sandbox" <?php echo ($settings['paypal_mode'] ?? 'sandbox') == 'sandbox' ? 'selected' : ''; ?>><?php _e('settings_paypal_sandbox'); ?></option>
                                    <option value="live" <?php echo ($settings['paypal_mode'] ?? '') == 'live' ? 'selected' : ''; ?>><?php _e('settings_paypal_live'); ?></option>
                                </select>
                            </div>

                            <!-- MercadoPago -->
                            <div class="section-subtitle"><i class="fa fa-credit-card"></i> <?php _e('settings_mercadopago'); ?> <span style="font-weight:400;color:#888;font-size:0.8rem;"><?php _e('settings_mp_regions'); ?></span></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _e('settings_mp_public'); ?></label>
                                        <input type="text" name="mercadopago_public_key" class="form-control" value="<?php echo htmlspecialchars($settings['mercadopago_public_key'] ?? ''); ?>" placeholder="APP_USR-..." style="font-family:monospace;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _e('settings_mp_token'); ?></label>
                                        <input type="password" name="mercadopago_access_token" class="form-control" value="" placeholder="••••••••••••••••" style="font-family:monospace;">
                                        <small><i class="fa fa-lock"></i> <?php _e('settings_key_hidden'); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <small>
                                    <i class="fa fa-info-circle"></i> <?php echo __('settings_mp_info'); ?>
                                    <br>
                                    <?php _e('settings_mp_webhook_url'); ?>: <code><?php echo $base_url_webhook; ?>/webhook_mercadopago.php</code> — <?php _e('settings_mp_events'); ?>
                                </small>
                            </div>

                            <!-- Transferencias Bancarias -->
                            <div class="section-subtitle"><i class="fa fa-university"></i> <?php _e('settings_bank_accounts'); ?></div>
                            <div class="form-group">
                                <label><?php _e('settings_bank_instructions_label'); ?></label>
                                <textarea name="payment_bank_instructions" class="form-control" rows="2"><?php echo htmlspecialchars($settings['payment_bank_instructions'] ?? 'Realiza tu transferencia a cualquiera de nuestras cuentas bancarias y envía el comprobante a nuestro correo para confirmar tu reserva.'); ?></textarea>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" id="banksTable">
                                    <thead>
                                        <tr>
                                            <th><?php _e('settings_bank_table_banco'); ?></th>
                                            <th><?php _e('settings_bank_table_type'); ?></th>
                                            <th><?php _e('settings_bank_table_account'); ?></th>
                                            <th><?php _e('settings_bank_table_holder'); ?></th>
                                            <th><?php _e('settings_bank_table_doc'); ?></th>
                                            <th><?php _e('settings_bank_table_docno'); ?></th>
                                            <th><?php _e('settings_bank_table_active'); ?></th>
                                            <th><?php _e('actions'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $bq = mysqli_query($connection, "SELECT * FROM bank_accounts ORDER BY sort_order ASC");
                                    while ($ba = mysqli_fetch_assoc($bq)):
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($ba['bank_name']); ?></td>
                                            <td><?php echo htmlspecialchars($ba['account_type']); ?></td>
                                            <td><code><?php echo htmlspecialchars($ba['account_number']); ?></code></td>
                                            <td><?php echo htmlspecialchars($ba['account_holder']); ?></td>
                                            <td><?php echo htmlspecialchars($ba['document_type']); ?></td>
                                            <td><?php echo htmlspecialchars($ba['document_number']); ?></td>
                                            <td><?php echo $ba['is_active'] ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>'; ?></td>
                                            <td>
                                                <a href="ajax.php?delete_bank=<?php echo $ba['id']; ?>&csrf=<?php echo csrf_token(); ?>" class="btn btn-danger btn-sm" style="border-radius:60px;" onclick="return confirm('<?php _e('confirm_delete'); ?>')"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#addBankForm">
                                <i class="fa fa-plus"></i> <?php _e('settings_bank_add'); ?>
                            </button>
                            <div class="collapse mt-2" id="addBankForm">
                                <div class="card card-body" style="background:#f8f9fa;border:1px solid var(--border);border-radius:8px;padding:16px;">
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <input type="text" name="bank_name" class="form-control form-control-sm" placeholder="<?php _e('settings_bank_placeholder_name'); ?>" form="bankForm">
                                        </div>
                                        <div class="col-md-2">
                                            <select name="account_type" class="form-control form-control-sm" form="bankForm">
                                                <option value="Corriente">Corriente</option>
                                                <option value="Ahorros">Ahorros</option>
                                                <option value="Digital">Digital (Nequi/Daviplata)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" name="account_number" class="form-control form-control-sm" placeholder="<?php _e('settings_bank_placeholder_account'); ?>" form="bankForm">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" name="account_holder" class="form-control form-control-sm" placeholder="<?php _e('settings_bank_placeholder_holder'); ?>" form="bankForm">
                                        </div>
                                        <div class="col-md-2">
                                            <select name="document_type" class="form-control form-control-sm" form="bankForm">
                                                <option value="NIT">NIT</option>
                                                <option value="CC">Cédula</option>
                                                <option value="RUC">RUC</option>
                                                <option value="RFC">RFC</option>
                                                <option value="CPF">CPF</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" name="document_number" class="form-control form-control-sm" placeholder="<?php _e('settings_bank_placeholder_docno'); ?>" form="bankForm">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-success btn-sm" name="save_bank_account" form="bankForm"><i class="fa fa-save"></i> <?php _e('settings_bank_save'); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg" name="save_settings">
                            <i class="fa fa-save"></i> <?php _e('settings_save_config'); ?>
                        </button>
                    </form>
                    <form id="bankForm" action="ajax.php" method="post"></form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.section-title {
    color: var(--primary);
    font-size: 1.15rem;
    font-weight: 700;
    border-bottom: 2px solid var(--border);
    padding-bottom: 10px;
    margin: 30px 0 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.section-title small {
    font-weight: 400;
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-left: 6px;
}
.section-subtitle {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text);
    margin: 16px 0 10px;
    padding-bottom: 6px;
    border-bottom: 1px dashed var(--border);
    display: flex;
    align-items: center;
    gap: 8px;
}
.settings-section {
    padding-left: 4px;
}
.preview-img {
    margin-top: 8px;
    border-radius: 6px;
    overflow: hidden;
    max-width: 200px;
    border: 1px solid var(--border);
}
.preview-img img {
    width: 100%;
    height: auto;
    display: block;
}
</style>
