<?php
/**
 * Script para popular banco de dados com dados de exemplo
 * Use este arquivo para inserir dados mockados que o usuário poderá editar/deletar
 * Acesso: /seed-data.php?action=insert
 */

require_once __DIR__ . '/config.php';

// Segurança: só permite acesso local ou com token
$allowAccess = false;
$isLocal = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', 'localhost', '::1']);
$isDocker = strpos($_SERVER['REMOTE_ADDR'], '172.') === 0;
$hasToken = isset($_GET['token']) && $_GET['token'] === md5(date('Y-m-d'));
$isAdmin = false;

if (isset($_SESSION['admin_id'])) {
    try {
        $db = getDB();
        $admin = $db->prepare("SELECT id FROM admin_users WHERE id = ?")->execute([$_SESSION['admin_id']]);
        $isAdmin = $admin !== false;
    } catch (Exception $ex) {}
}

$allowAccess = $isLocal || $isDocker || $hasToken || $isAdmin;

if (!$allowAccess) {
    http_response_code(403);
    die('Access denied. Use /seed-data.php?token=' . md5(date('Y-m-d')) . ' or access from localhost');
}

$db = getDB();
$msg = '';
$error = '';

if (isset($_GET['action']) && $_GET['action'] === 'insert') {
    try {
        // Serviços
        $services = [
            ['Consulta Clínica Geral', 'Atendimento veterinário completo com anamnese, avaliação física e diagnóstico. Indicado para rotina, vacinas e check-ups periódicos.', 'fas fa-stethoscope', '2.jpeg', 'gostaria de agendar uma consulta clínica geral', 1],
            ['Medicina Equina', 'Especialização em clínica geral de equinos. Atendimento no estábulo com equipamentos portáteis para sua segurança e conforto.', 'fas fa-horse', '8.jpeg', 'preciso de um veterinário para meu cavalo', 2],
            ['Pequenos Animais', 'Clínica geral para cães e gatos com diagnóstico por imagem, ultrassom e laboratorial. Vacinação, vermifugação e atendimentos preventivos.', 'fas fa-paw', '1.jpeg', 'tenho dúvidas sobre a saúde do meu pet', 3],
            ['Animais Silvestres', 'Atendimento especializado para aves, répteis, pequenos mamíferos e outros silvestres. Reabilitação e cuidados específicos de cada espécie.', 'fas fa-eagle', '5.jpeg', 'tenho um animal silvestre que precisa de atendimento', 4],
            ['Cirurgias Veterinárias', 'Procedimentos cirúrgicos de pequeno e médio porte com anestesia segura e monitoramento contínuo. Centro cirúrgico equipado com tecnologia moderna.', 'fas fa-syringe', '9.jpeg', 'meu animal precisa de cirurgia', 5],
            ['Ultrassom e Diagnóstico', 'Ultrassom abdominal, torácico e obstétrico. Diagnóstico por imagem de alta qualidade para precisão em tratamentos.', 'fas fa-waveform-lines', '7.jpeg', 'gostaria de fazer um ultrassom', 6],
        ];
        
        $db->query("DELETE FROM services");
        foreach ($services as $svc) {
            $db->prepare("INSERT INTO services (title, description, icon, image, whatsapp_text, sort_order, active) VALUES (?, ?, ?, ?, ?, ?, 1)")
                ->execute($svc);
        }
        
        // Equipe
        $team = [
            ['Dra. Samla Cristie', 'Médica Veterinária - Proprietária', 'Formada pela Universidade Estadual de Goiás. Especialista em clínica geral com foco em medicina equina e animais de produção. Dedicada a levar medicina de qualidade ao campo.', '3.jpeg', 1],
            ['Dr. Carlos Santos', 'Cirurgião Veterinário', 'Especialista em procedimentos cirúrgicos com 12 anos de experiência. Realiza desde pequenas intervenções até cirurgias complexas com segurança.', '10.jpeg', 2],
            ['Dra. Marina Lima', 'Clínica de Pequenos Animais', 'Apaixonada por cães e gatos. Realiza diagnósticos avançados em ultrassom e oferece acompanhamento integral dos seus animaizinhos.', '11.jpeg', 3],
            ['Tecnólogo Anderson', 'Auxiliar Técnico', 'Responsável por exames laboratoriais e preparação do centro cirúrgico. Garante esterilização e segurança em todos os procedimentos.', '12.jpeg', 4],
        ];
        
        $db->query("DELETE FROM team");
        foreach ($team as $m) {
            $db->prepare("INSERT INTO team (name, role, description, image, sort_order, active) VALUES (?, ?, ?, ?, ?, 1)")
                ->execute($m);
        }
        
        // Depoimentos
        $testimonials = [
            ['Roberto Oliveira', 'RO', '#FF6B6B', 5.0, 'Excelente atendimento! A Dra. Samla é muito atenciosa e cuidadosa com o meu cavalo. Recomendo para todos os proprietários de equinos na região.', '4 semanas atrás', 1],
            ['Juliana Costa', 'JC', '#4ECDC4', 5.0, 'Levei meu cachorro com cólica e a equipe foi rápida no diagnóstico. Procedimento seguro e meu pet chegou em casa recuperado e feliz.', '3 semanas atrás', 2],
            ['Felipe Mendes', 'FM', '#95E1D3', 5.0, 'Profissionais de excelência! A Dra. Samla opera meus animais de trabalho há 3 anos. Confiança total no trabalho dela.', '2 semanas atrás', 3],
            ['Patricia Silva', 'PS', '#F38181', 5.0, 'Meus gatos adoram quando vamos lá. O ambiente é tranquilo e a equipe muito carinhosa com os animais.', '1 semana atrás', 4],
            ['Marcelo Rocha', 'MR', '#AA96DA', 5.0, 'Encontrei a veterinária que procurava! Competente, dedicada e com um olhar humanizado para com os animais. Muito obrigado!', 'Há 2 dias', 5],
        ];
        
        $db->query("DELETE FROM testimonials");
        foreach ($testimonials as $t) {
            $db->prepare("INSERT INTO testimonials (name, initials, color, rating, text, date_label, sort_order, active) VALUES (?, ?, ?, ?, ?, ?, ?, 1)")
                ->execute($t);
        }
        
        // Artigos
        $articles = [
            ['Cuidados Essenciais com o Cavalo no Verão', 'cuidados-cavalo-verao', 'O calor extremo do verão pode afetar a saúde dos equinos. Conheça as principais medidas para manter seu cavalo saudável durante essa estação.', '<h2>Hidratação é Fundamental</h2><p>Durante o verão, os cavalos perdem muito líquido através da transpiração. Oferça água fresca em abundância, preferencialmente em múltiplos bebedouros para evitar competição entre os animais.</p><h2>Proteção Solar</h2><p>Animais com pelagem clara são mais propensos a queimaduras solares. Use roupas protetoras ou oferça sombra durante os períodos mais quentes do dia. Aplicar protetor solar em áreas sensíveis é recomendado.</p><h2>Alimentação Adequada</h2><p>Reduza volumosos muito fibrosos e aumente alimentos com maior digestibilidade. A fenaçã picada é uma ótima opção para o verão.</p><h2>Exercício Moderado</h2><p>Evite exercícios intensos durante as horas mais quentes. Prefira treinar no início da manhã ou ao entardecer. Ofereça muita água após o exercício.</p>', '6.jpeg', 'Cuidados Equinos', '4 min', 0],
            ['Sinais de que Seu Cão Pode Estar Com Dor', 'sinais-cao-com-dor', 'Muitas vezes os cães escondem sua dor. Saiba quais são os sinais de alerta que não devem ser ignorados.', '<h2>Mudanças no Comportamento</h2><p>Um cachorro com dor frequentemente se torna mais isolado, agressivo ou ansioso. Procure por mudanças súbitas na personalidade do seu pet.</p><h2>Dificuldades de Locomoção</h2><p>Manqueira, relutância em pular ou subir escadas, e dificuldade para levantar são sinais claros de desconforto nas articulações ou músculos.</p><h2>Alterações no Apetite</h2><p>Perda de apetite é frequentemente um sinal de dor crônica. Se seu cão para de comer normalmente, investigar com um veterinário é essencial.</p><h2>Sinais Físicos</h2><p>Procure por inflamação, feridas, sensibilidade ao toque ou alterações na respiração. Esses sinais indicam que uma avaliação veterinária é necessária.</p>', '5.jpeg', 'Pequenos Animais', '5 min', 0],
            ['Vacinação em Gatos: Tudo o que Você Precisa Saber', 'vacinacao-gatos-guia', 'Entenda o calendário vacinal para gatos e por que cada vacina é importante para a prevenção de doenças.', '<h2>Vacinação Inicial</h2><p>Kittens devem receber as primeiras vacinas aos 6-8 semanas de idade. A série inicial inclui 3 doses com intervalo de 3-4 semanas entre elas.</p><h2>Vacinas Essenciais (Core Vaccines)</h2><p>A tríplice felina protege contra rinotraqueíte viral, calicivírus e panleucopenia. Esta é essencial para todos os gatos, independentemente do estilo de vida.</p><h2>Vacinas Não Essenciais</h2><p>A vacina contra leucemia felina (FeLV) e raiva são recomendadas dependendo do risco de exposição do seu gato.</p><h2>Revacinação</h2><p>Após a série inicial, seu gato precisa de reforços anuais. Algumas vacinas podem ser feitas a cada 3 anos conforme orientação do veterinário.</p>', '1.jpeg', 'Pequenos Animais', '4 min', 0],
            ['Como Identificar e Prevenir Problemas Bucais em Pets', 'saude-bucal-pets', 'A saúde bucal é muitas vezes negligenciada, mas é crucial para a qualidade de vida dos animais. Conheça os principais problemas e como preveni-los.', '<h2>Sinais de Problema Bucal</h2><p>Halitose (mau hálito), dificuldade para comer, salivação excessiva e sangramento das gengivas são sinais de alerta.</p><h2>Limpeza Preventiva</h2><p>A escovação regular dos dentes é a melhor prevenção contra tártaro e doença periodontal. Comece cedo e faça uma rotina diária.</p><h2>Alimentos e Brinquedos Apropriados</h2><p>Alimentos secos adequados e brinquedos mastigáveis podem ajudar a manter os dentes limpos naturalmente.</p><h2>Avaliação Profissional</h2><p>Limpezas profissionais periódicas são recomendadas pela maioria dos veterinários para prevenir complicações sérias.</p>', '8.jpeg', 'Prevenção', '5 min', 0],
            ['Nutrição Adequada para Répteis e Aves', 'nutricao-repteis-aves', 'Cada espécie silvestre tem necessidades nutricionais específicas. Descubra como oferecer uma alimentação balanceada.', '<h2>Necessidades Diferentes por Espécie</h2><p>Não existe uma dieta única para todos os répteis e aves. Serpentes necessitam de presas, enquanto papagaios precisam de frutas, vegetais e sementes balanceadas.</p><h2>Suplementação Essencial</h2><p>Cálcio e vitamina D3 são críticos para a saúde óssea. Deficiências podem levar a problemas graves de desenvolvimento e fratura.</p><h2>Frequência de Alimentação</h2><p>A idade e espécie determinam com que frequência alimentar. Filhotes necessitam alimentação mais frequente que adultos.</p><h2>Variação e Qualidade</h2><p>Ofereça variedade de alimentos para garantir nutrientes balanceados. Sempre utilize alimentos/presas de qualidade e procedência confiável.</p>', '12.jpeg', 'Animais Silvestres', '6 min', 0],
        ];
        
        $db->query("DELETE FROM articles");
        foreach ($articles as $a) {
            $db->prepare("INSERT INTO articles (title, slug, excerpt, content, image, category, read_time, featured, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)")
                ->execute($a);
        }
        
        // FAQ
        $faq = [
            ['Geral', 'fas fa-question-circle', 'Qual o horário de atendimento?', 'Atendemos de segunda a sexta-feira das 8h às 18h, e aos sábados das 8h às 12h. Agendamentos podem ser feitos via WhatsApp ou telefone.', 1],
            ['Geral', 'fas fa-question-circle', 'Fazem atendimento domiciliar?', 'Sim! Realizamos visitas para animais de produção e equinos nas propriedades rurais da região. O valor da visita é cobrado conforme a distância.', 2],
            ['Equinos', 'fas fa-horse', 'Qual a frequência de vacinação para cavalos?', 'Recomendamos vacinação contra tétano anualmente. A vacinação contra influenza equina é recomendada a cada 6 meses. Consulte o veterinário para um plano personalizado.', 1],
            ['Equinos', 'fas fa-horse', 'Como identificar cólica em equinos?', 'Sinais incluem agitação excessiva, rolamento frequente, recusa em comer, sudoração e postura anormal. A cólica é emergência e requer atendimento imediato.', 2],
            ['Pequenos Animais', 'fas fa-paw', 'Com que idade posso castrar meu cão ou gato?', 'Geralmente recomendamos castração a partir dos 6 meses de idade. Em alguns casos, pode ser feito mais cedo. Consulte o veterinário para orientação específica.', 1],
            ['Pequenos Animais', 'fas fa-paw', 'Qual é o calendário vacinal recomendado?', 'Filhotes recebem 3 doses de vacina polivalente com intervalo de 3-4 semanas. Reforço anual é necessário. Raiva é obrigatória por lei em alguns municípios.', 2],
            ['Cirurgias', 'fas fa-syringe', 'Como preparar meu animal para cirurgia?', 'Jejum de 6-8 horas é obrigatório. Realizar exames pré-operatórios é recomendado. A equipe fornecerá instruções detalhadas na marcação.', 1],
            ['Cirurgias', 'fas fa-syringe', 'Qual é o pós-operatório recomendado?', 'Repouso absoluto por 10-14 dias, cuidados com a ferida, medicações conforme prescrito e retorno para avaliar evolução. Os pontos são removidos em 10-14 dias.', 2],
        ];
        
        $db->query("DELETE FROM faq");
        foreach ($faq as $f) {
            $db->prepare("INSERT INTO faq (category, category_icon, question, answer, sort_order, active) VALUES (?, ?, ?, ?, ?, 1)")
                ->execute($f);
        }
        
        // Especialidades
        $specialties = [
            ['Clínica Geral', 'fas fa-stethoscope', 1],
            ['Cirurgia Veterinária', 'fas fa-syringe', 2],
            ['Medicina Equina', 'fas fa-horse', 3],
            ['Animais Silvestres', 'fas fa-eagle', 4],
        ];
        
        $db->query("DELETE FROM specialties");
        foreach ($specialties as $s) {
            $db->prepare("INSERT INTO specialties (name, icon, sort_order, active) VALUES (?, ?, ?, 1)")
                ->execute($s);
        }
        
        // Clientes
        $clients = [
            ['Clínica Vera Cruz - Pet', 'Clínica Veterinária', 'Parceiros em atendimento a pequenos animais na zona urbana. Referência em terapias inovadoras.', 'fas fa-clinic-medical', '#FF6B6B', 'Goiânia, GO', 1],
            ['Haras Santa Maria', 'Propriedade Rural', 'Grande propriedade especializada em criação de cavalos de raça. Confiaram na Dra. Samla há mais de 3 anos.', 'fas fa-horse', '#4ECDC4', 'Região Rural, GO', 2],
            ['PetShop Animal House', 'Pet Shop', 'Parceria contínua para vacinação, vermifugação e orientações ao cliente. Excelente relacionamento comercial.', 'fas fa-shop', '#95E1D3', 'Goiânia, GO', 3],
            ['Fazenda Três Irmãos', 'Propriedade Rural', 'Atendemos regularmente para sanidade de rebanho e atendimentos emergenciais. Muito satisfeitos com nossos serviços.', 'fas fa-barn', '#F38181', 'Interior, GO', 4],
            ['Instituto de Fauna Silvestre', 'ONG Ambiental', 'Parceria em projetos de reabilitação e soltura de animais silvestres. Contribuindo com preservação ambiental.', 'fas fa-leaf', '#AA96DA', 'Goiânia, GO', 5],
            ['Hospital Veterinário Saúde Animal', 'Hospital Veterinário', 'Referência em procedimentos cirúrgicos complexos. Realizamos interconsultas e encaminhamentos especializados.', 'fas fa-hospital', '#FFB6C1', 'Goiânia, GO', 6],
        ];
        
        $db->query("DELETE FROM clients");
        foreach ($clients as $c) {
            $db->prepare("INSERT INTO clients (name, type, description, logo_icon, logo_color, location, sort_order, active) VALUES (?, ?, ?, ?, ?, ?, ?, 1)")
                ->execute($c);
        }
        
        $msg = '✅ Todos os dados de exemplo foram inseridos com sucesso!<br><br>
        <strong>Resumo:</strong><br>
        ✓ 6 Serviços<br>
        ✓ 4 Membros da Equipe<br>
        ✓ 5 Depoimentos<br>
        ✓ 5 Artigos de Blog<br>
        ✓ 8 Perguntas FAQ<br>
        ✓ 4 Especialidades<br>
        ✓ 6 Clientes<br><br>
        Agora você pode gerenciar esses dados através da <strong><a href="/admin/">área de administração</a></strong>. 
        Mantenha os que quiser e delete os que não precisar!';
        
    } catch (Exception $ex) {
        $error = 'Erro ao inserir dados: ' . $ex->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserir Dados de Exemplo</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #2d5016 0%, #4a7c2c 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { background: white; border-radius: 12px; padding: 40px; max-width: 600px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #2d5016; font-size: 1.8rem; margin-bottom: 10px; }
        .header p { color: #666; font-size: 1rem; }
        .icon { font-size: 2.5rem; margin-bottom: 10px; }
        .alert { padding: 15px 20px; border-radius: 8px; margin: 20px 0; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert a { color: #155724; text-decoration: underline; }
        .alert-error a { color: #721c24; }
        .button-group { display: flex; gap: 10px; margin-top: 30px; }
        .btn { padding: 12px 24px; border-radius: 8px; font-size: 1rem; font-weight: 600; border: none; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; transition: all 0.3s; }
        .btn-primary { background: #2d5016; color: white; flex: 1; }
        .btn-primary:hover { background: #1e3710; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(45, 80, 22, 0.3); }
        .btn-secondary { background: #f0f0f0; color: #333; flex: 1; }
        .btn-secondary:hover { background: #e0e0e0; }
        .info-box { background: #f9f9f9; border-left: 4px solid #2d5016; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .info-box strong { color: #2d5016; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🏥</div>
            <h1>Dados de Exemplo</h1>
            <p>Popule seu sistema com dados mockados</p>
        </div>

        <?php if ($msg): ?>
            <div class="alert alert-success"><?= $msg ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php else: ?>
            <div class="info-box">
                <strong>ℹ️ Como usar:</strong><br>
                Clique no botão abaixo para inserir dados de exemplo no seu banco de dados. Esses dados podem ser gerenciados (editados ou deletados) através da área de administração.
            </div>

            <div class="info-box">
                <strong>📋 O que será inserido:</strong>
                <ul style="margin: 10px 0 0 20px;">
                    <li>6 Serviços veterinários</li>
                    <li>4 Membros da equipe profissional</li>
                    <li>5 Depoimentos de clientes</li>
                    <li>5 Artigos de blog</li>
                    <li>8 Perguntas frequentes (FAQ)</li>
                    <li>4 Especialidades</li>
                    <li>6 Clientes parceiros</li>
                </ul>
            </div>

            <div class="button-group">
                <a href="?action=insert" class="btn btn-primary" onclick="return confirm('Tem certeza? Isso vai substituir dados existentes.');">✅ Inserir Dados</a>
                <a href="/admin/" class="btn btn-secondary">📊 Ir para Admin</a>
            </div>
        <?php endif; ?>

        <?php if ($msg): ?>
            <div class="button-group">
                <a href="/" class="btn btn-secondary">← Voltar ao Site</a>
                <a href="/admin/" class="btn btn-primary">📊 Área de Admin</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
