<?php $this->load->view('layout/navbar'); ?>
<div class="page-wrap">
    <?php $this->load->view('layout/sidebar.php'); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="ik ik-message-circle bg-blue"></i>
                            <div class="d-inline">
                                <h5> <?php echo $titulo; ?> </h5>
                                <span><?php echo $subtitulo; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-block">
                            <h3>Comentarios</h3>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <?php if (empty($comments)) : ?>
                                    <div class="alert alert-info">No hay comentarios registrados para esta solicitud.</div>
                                <?php else : ?>
                                    <?php foreach ($comments as $c) : ?>
                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1 small"><strong><?php echo htmlspecialchars($c->username ?: 'Sistema'); ?></strong> <small class="text-muted">(<?php echo htmlspecialchars($c->action); ?>)</small></h5>
                                                <small class="text-muted"><?php echo isset($c->created_at) ? $c->created_at : ''; ?></small>
                                            </div>
                                            <p class="mb-1"><?php echo nl2br(htmlspecialchars($c->comment)); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <a href="<?php echo base_url($this->router->fetch_class()); ?>" class="btn btn-secondary mt-3">Volver a Solicitudes</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>