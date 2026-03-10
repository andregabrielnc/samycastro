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
        // Configurações do Site
        $settings = [
            ['hero_title', 'Dra. Samla Cristie', 'hero', 'Título Principal', 'text', 1],
            ['hero_subtitle', 'CRMV-GO 14064-VP', 'hero', 'Subtítulo (Registro)', 'text', 2],
            ['hero_tag', 'Medicina Veterinária com Amor e Dedicação', 'hero', 'Frase de Destaque', 'text', 3],
            ['hero_description', 'Cuidando dos seus animais com carinho, competência e paixão pelo campo. Atendimento domiciliar e rural para cães, gatos, equinos e animais silvestres.', 'hero', 'Descrição', 'textarea', 4],
            ['hero_image', '4.jpeg', 'hero', 'Imagem de Fundo', 'image', 5],
            ['about_title', 'Uma paixão que começou no campo', 'sobre', 'Título Sobre', 'text', 1],
            ['about_text1', 'Sou a <strong>Dra. Samla Cristie</strong>, médica veterinária registrada sob o CRMV-GO 14064-VP, apaixonada por animais desde a infância. Cresci em meio à natureza, rodeada de cavalos, cães e a fauna silvestre do cerrado goiano, e essa vivência moldou minha vocação.', 'sobre', 'Parágrafo 1', 'textarea', 2],
            ['about_text2', 'Formada em Medicina Veterinária, me especializei no atendimento ambulatorial e domiciliar, levando cuidado profissional diretamente onde o animal vive. Acredito que o ambiente familiar reduz o estresse do paciente e proporciona uma avaliação mais completa do seu dia a dia.', 'sobre', 'Parágrafo 2', 'textarea', 3],
            ['about_text3', 'Minha abordagem combina a <strong>medicina veterinária moderna</strong> com o <strong>respeito à natureza e ao bem-estar animal</strong>. Cada animal é único, e meu compromisso é oferecer um atendimento humanizado, acessível e de excelência.', 'sobre', 'Parágrafo 3', 'textarea', 4],
            ['about_image1', '3.jpeg', 'sobre', 'Imagem Principal', 'image', 5],
            ['about_image2', '7.jpeg', 'sobre', 'Imagem Secundária', 'image', 6],
            ['about_stat1_number', '500+', 'sobre', 'Estatística 1 - Número', 'text', 7],
            ['about_stat1_label', 'Atendimentos', 'sobre', 'Estatística 1 - Label', 'text', 8],
            ['about_stat2_number', '4', 'sobre', 'Estatística 2 - Número', 'text', 9],
            ['about_stat2_label', 'Especialidades', 'sobre', 'Estatística 2 - Label', 'text', 10],
            ['about_stat3_number', '100%', 'sobre', 'Estatística 3 - Número', 'text', 11],
            ['about_stat3_label', 'Dedicação', 'sobre', 'Estatística 3 - Label', 'text', 12],
            ['cta_title', 'Agende uma Consulta', 'cta', 'Título CTA', 'text', 1],
            ['cta_text', 'Nossa equipe está a postos para cuidar do seu animal com todo carinho e profissionalismo. Não espere — prevenir é sempre o melhor remédio!', 'cta', 'Texto CTA', 'textarea', 2],
            ['cta_image', '6.jpeg', 'cta', 'Imagem Fundo CTA', 'image', 3],
            ['team_intro1', 'Nossa equipe é composta por profissionais com formação sólida e experiência prática nas mais diversas áreas da medicina veterinária. Atuamos com excelência em <strong>cirurgia geral</strong>, <strong>cirurgia ortopédica</strong>, <strong>nutrição animal</strong>, <strong>clínica de pequenos e grandes animais</strong>, <strong>medicina preventiva</strong> e <strong>cuidados intensivos</strong>.', 'equipe', 'Texto Intro Equipe 1', 'textarea', 1],
            ['team_intro2', 'Investimos constantemente em <strong>educação continuada</strong>, participando de congressos, simpósios e cursos de atualização para trazer sempre as melhores práticas e técnicas inovadoras aos nossos pacientes. Nosso diferencial é a combinação de conhecimento técnico com uma abordagem humanizada, tratando cada animal como se fosse nosso.', 'equipe', 'Texto Intro Equipe 2', 'textarea', 2],
            ['whatsapp_number', '5562994793553', 'contato', 'WhatsApp (só números)', 'text', 1],
            ['whatsapp_display', '(62) 99479-3553', 'contato', 'WhatsApp (exibição)', 'text', 2],
            ['contact_location', 'Goiânia e região - GO', 'contato', 'Localização', 'text', 3],
            ['contact_location_detail', 'Atendimento domiciliar e rural', 'contato', 'Detalhe Localização', 'text', 4],
            ['contact_email', 'contato@drasamlacristie.com.br', 'contato', 'E-mail', 'text', 5],
            ['hours_weekday', 'Seg a Sex: 08:00 às 18:00', 'contato', 'Horário Semana', 'text', 6],
            ['hours_saturday', 'Sáb: 08:00 às 12:00', 'contato', 'Horário Sábado', 'text', 7],
            ['footer_text', 'A melhor assistência veterinária para seus animais, unindo amor, competência e dedicação. Atendimento domiciliar e rural com toda a estrutura que seu animal merece.', 'contato', 'Texto Rodapé', 'textarea', 8],
            ['blog_cta_title', '🐾 Agende uma Consulta — Nossa Equipe Está a Postos!', 'blog', 'Título CTA Blog', 'text', 1],
            ['blog_cta_text', 'Prevenir é o melhor caminho para uma vida longa e saudável para o seu animal. Não deixe para depois! Nossa equipe está pronta para oferecer o melhor atendimento veterinário, com amor e dedicação, diretamente no conforto da sua casa ou propriedade rural.', 'blog', 'Texto CTA Blog', 'textarea', 2],
        ];

        foreach ($settings as $s) {
            $db->prepare("INSERT INTO settings (setting_key, setting_value, setting_group, setting_label, setting_type, sort_order) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)")
                ->execute($s);
        }

        // Serviços
        $services = [
            ['Consulta para Cães','A consulta veterinária para cães é realizada no conforto do lar do animal, onde o pet se sente mais seguro e tranquilo. Realizamos avaliação clínica completa: auscultação cardiopulmonar, exame físico detalhado, verificação de mucosas, hidratação, palpação abdominal e avaliação comportamental. Também orientamos sobre vacinação, vermifugação, nutrição adequada e prevenção de doenças. O atendimento domiciliar permite observar o ambiente onde o animal vive, identificando possíveis riscos à saúde.','fas fa-dog','18.jpeg','gostaria de agendar consulta para meu cão.',1],
            ['Consulta para Gatos','Gatos são naturalmente estressados em ambientes desconhecidos, por isso o atendimento domiciliar é ideal para felinos. A consulta inclui exame clínico completo, avaliação de pelagem, condição corporal, saúde bucal e comportamento. Verificamos calendário vacinal, orientamos sobre castração, desparasitação e enriquecimento ambiental. Oferecemos também orientações sobre nutrição felina, prevenção de doenças urinários (muito comuns em gatos) e cuidados específicos para cada fase da vida do seu gato.','fas fa-cat','10.jpeg','gostaria de agendar consulta para meu gato.',2],
            ['Consulta para Equinos','O atendimento equino é realizado diretamente na propriedade rural, haras ou cocheira onde o animal se encontra. A consulta abrange exame clínico geral, avaliação de cascos, dentição, sistema locomotor e respiratório. Realizamos vermifugação estratégica, vacinação, exames reprodutivos e orientações nutricionais específicas para cavalos de trabalho, esporte ou lazer. Também oferecemos suporte em emergências como cólicas equinas, ferimentos e avaliação pré-compra.','fas fa-horse','5.jpeg','gostaria de agendar consulta para meu equino.',3],
            ['Animais Silvestres e Rurais','Atendemos animais silvestres legalizados e animais de produção rural. A consulta é feita no ambiente do animal, respeitando suas particularidades e necessidades específicas. Para silvestres, avaliamos condição geral, nutrição, enriquecimento ambiental e possíveis zoonoses. Para bovinos e outros animais de produção, realizamos exames clínicos, vacinação, manejo reprodutivo e sanitário. Cada espécie possui protocolos próprios de manuseio e contenção, garantindo segurança para o animal e a equipe.','fas fa-dove','12.jpeg','gostaria de agendar consulta para animal silvestre.',4],
            ['Consultas Virtuais','Oferecemos teleconsultas veterinárias para orientação nutricional, acompanhamento pós-operatório, avaliação comportamental, segunda opinião clínica e dúvidas gerais sobre a saúde do seu animal. A consulta virtual é ideal para tutores que precisam de orientação profissional sem sair de casa. Atendemos por videochamada com horário agendado, proporcionando praticidade sem comprometer a qualidade do atendimento. Orientamos sobre dieta adequada, suplementação, manejo comportamental, cuidados com filhotes e acompanhamento de tratamentos em andamento.','fas fa-video','9.jpeg','gostaria de agendar uma consulta virtual.',5],
            ['Parceria Técnica entre Clínicas','Oferecemos serviços veterinários terceirizados para clínicas e pet shops que precisam de suporte profissional especializado. Nossa parceria inclui: cirurgias gerais e ortopédicas sob demanda, atendimento ambulatorial em horários complementares, cobertura de emergências e plantões, consultoria em nutrição animal e protocolos clínicos, e apoio diagnóstico em casos complexos. Benefícios: redução de custos operacionais, acesso a profissionais capacitados sem vínculo fixo, flexibilidade de horários, qualidade garantida com registro CRMV, e ampliação do portfólio de serviços da sua clínica sem investimento em contratação.','fas fa-handshake','8.jpeg','gostaria de saber sobre parceria técnica para minha clínica.',6],
        ];
        
        $db->query("DELETE FROM services");
        foreach ($services as $svc) {
            $db->prepare("INSERT INTO services (title, description, icon, image, whatsapp_text, sort_order, active) VALUES (?, ?, ?, ?, ?, ?, 1)")
                ->execute($svc);
        }
        
        // Equipe
        $team = [
            ['Dra. Samla Cristie','Médica Veterinária — CRMV-GO 14064-VP','Especialista em clínica de pequenos e grandes animais, cirurgia geral e atendimento ambulatorial domiciliar.','2.jpeg',1],
            ['Capacitação Contínua','Palestras e Congressos','Participação ativa em eventos científicos, ministrando palestras sobre tratamentos inovadores e saúde animal.','9.jpeg',2],
            ['Vacinação e Medicação','Protocolos Atualizados','Seguimos protocolos rigorosos de vacinação e medicação, utilizando produtos de qualidade e técnicas seguras de aplicação.','16.jpeg',3],
            ['Atendimento Equino','Manejo e Cuidados','Atendimento especializado para equinos com foco em ortopedia, nutrição e manejo reprodutivo diretamente na propriedade.','15.jpeg',4],
        ];
        
        $db->query("DELETE FROM team");
        foreach ($team as $m) {
            $db->prepare("INSERT INTO team (name, role, description, image, sort_order, active) VALUES (?, ?, ?, ?, ?, 1)")
                ->execute($m);
        }
        
                // Depoimentos
        $testimonials = [
            ['Maria Fernanda S.','MF','#4285f4',5.0,'A Dra. Samla é simplesmente maravilhosa! Atendeu meus dois cachorros em casa, com muita paciência e carinho. Explicou tudo direitinho sobre a vacinação e os cuidados. Super recomendo!','há 2 semanas',1],
            ['João Carlos M.','JC','#ea4335',5.0,'Chamei a Dra. Samla para atender meus cavalos na fazenda. Profissional incrível, pontual e muito competente. Fez exames completos e orientou sobre o manejo nutricional. Nota 10!','há 1 mês',2],
            ['Ana Clara R.','AC','#fbbc05',5.0,'Minha gatinha Mimi ficou doente e a Dra. Samla veio atender em casa no mesmo dia. Muito atenciosa, tratou com muito carinho e a Mimi melhorou super rápido. Deus abençoe!','há 1 mês',3],
            ['Pedro Henrique L.','PH','#34a853',4.5,'Excelente profissional! Atendeu meu bezerro que estava com problemas respiratórios. Muito dedicada e conhece muito sobre animais de produção. O atendimento rural dela é diferenciado.','há 2 meses',4],
            ['Luciana B.','LB','#673ab7',5.0,'Melhor veterinária da região! A Dra. Samla cuidou da cirurgia do meu dog e foi impecável. O pós-operatório foi excelente e ela acompanhou cada detalhe. Muito grata!','há 3 meses',5],
            ['Renato F.','RF','#ff5722',5.0,'A Dra. Samla atendeu meus cavalos de vaquejada e mostrou um conhecimento excepcional em equinos. Muito profissional, atenciosa e apaixonada pelo que faz. Recomendo demais!','há 3 meses',6],
        ];
        
        $db->query("DELETE FROM testimonials");
        foreach ($testimonials as $t) {
            $db->prepare("INSERT INTO testimonials (name, initials, color, rating, text, date_label, sort_order, active) VALUES (?, ?, ?, ?, ?, ?, ?, 1)")
                ->execute($t);
        }
        
                // Artigos
        $articles = [
            ['Cuidados Essenciais com Cães: Guia Completo','cuidados-caes-guia','Seu cão precisa de atenção especial em todas as fases da vida. Saiba quais são os sinais de alerta, a importância do check-up anual, como manter a vacinação em dia e dicas de nutrição adequada para cada porte. Não esqueça da vermifugação trimestral e do controle de ectoparasitas (pulgas e carrapatos), fundamentais para a saúde do pet.','Seu cão precisa de atenção especial em todas as fases da vida. Saiba quais são os sinais de alerta, a importância do check-up anual, como manter a vacinação em dia e dicas de nutrição adequada para cada porte. Não esqueça da vermifugação trimestral e do controle de ectoparasitas (pulgas e carrapatos), fundamentais para a saúde do pet.','11.jpeg','Cães','5 min',0],
            ['Gatos: Sinais de Doença que Você Precisa Conhecer','gatos-sinais-doenca','Gatos são mestres em esconder dor e desconforto. Aprenda a identificar os sinais sutis de que algo não está bem: mudança de comportamento, perda de apetite, secreção nasal, alteração na urina e isolamento. A detecção precoce pode salvar a vida do seu felino!','Gatos são mestres em esconder dor e desconforto. Aprenda a identificar os sinais sutis de que algo não está bem: mudança de comportamento, perda de apetite, secreção nasal, alteração na urina e isolamento. A detecção precoce pode salvar a vida do seu felino!','17.jpeg','Gatos','4 min',0],
            ['Calendário de Vacinação para Equinos','calendario-vacinacao-equinos','Manter o calendário vacinal dos cavalos em dia é essencial para prevenir doenças como influenza equina, encefalomielite, tétano e raiva. Confira o cronograma completo e saiba quando vermifugar e realizar exames de rotina para manter seu cavalo saudável.','Manter o calendário vacinal dos cavalos em dia é essencial para prevenir doenças como influenza equina, encefalomielite, tétano e raiva. Confira o cronograma completo e saiba quando vermifugar e realizar exames de rotina para manter seu cavalo saudável.','8.jpeg','Equinos','6 min',0],
            ['Quando Levar seu Animal ao Veterinário: Guia de Emergência','quando-levar-animal','Saiba identificar situações de urgência e emergência veterinária: convulsões, dificuldade respiratória, envenenamento, sangramento, diarreia persistente e fratura. Ter um plano de ação pode salvar a vida do seu pet. Conheça os primeiros socorros para animais.','Saiba identificar situações de urgência e emergência veterinária: convulsões, dificuldade respiratória, envenenamento, sangramento, diarreia persistente e fratura. Ter um plano de ação pode salvar a vida do seu pet. Conheça os primeiros socorros para animais.','1.jpeg','Dicas Gerais','7 min',0],
        ];
        
        $db->query("DELETE FROM articles");
        foreach ($articles as $a) {
            $db->prepare("INSERT INTO articles (title, slug, excerpt, content, image, category, read_time, featured, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)")
                ->execute($a);
        }
        
                // FAQ
        $faq = [
            ['Cães e Gatos','fas fa-paw','Qual a frequência ideal de consultas para cães e gatos?','Para animais saudáveis, recomenda-se consultas anuais de check-up. Filhotes e animais idosos devem ser acompanhados com mais frequência — a cada 3 a 6 meses. Animais com doenças crônicas precisam de acompanhamento mais próximo, conforme orientação do veterinário.',1],
            ['Cães e Gatos','fas fa-paw','Quando devo iniciar a vacinação do meu filhote?','A vacinação de filhotes de cães geralmente inicia entre 6 a 8 semanas de vida, com a vacina V8 ou V10 (polivalente), seguida de reforços a cada 21-30 dias até completar 3 doses. Para gatos, inicia-se com a tríplice felina (V3 ou V4) na mesma idade. A vacina antirrábica é aplicada a partir de 4 meses de idade.',2],
            ['Cães e Gatos','fas fa-paw','Qual a importância da castração?','A castração previne doenças graves como tumores mamários, piometra (infecção uterina) e problemas prostáticos. Além disso, reduz comportamentos indesejados como marcação territorial, agressividade e fugas. O procedimento é seguro e recomendado a partir dos 6 meses de idade.',3],
            ['Equinos','fas fa-horse','Com que frequência devo vermifugar meu cavalo?','Recomenda-se a vermifugação estratégica de equinos, idealmente com exame de OPG (contagem de ovos por grama de fezes) para guiar o protocolo. Em geral, cavalos adultos são vermifugados a cada 3-4 meses, mas animais jovens e éguas podem precisar de protocolos mais frequentes.',1],
            ['Equinos','fas fa-horse','O que é cólica equina e como prevenir?','Cólica equina é toda dor abdominal no cavalo, podendo ter diversas causas: alimentação inadequada, verminoses, torção intestinal, entre outras. É uma emergência veterinária! Para prevenir: mantenha alimentação regular e de qualidade, água limpa sempre disponível, vermifugação em dia e evite mudanças bruscas na dieta.',2],
            ['Equinos','fas fa-horse','Quais vacinas são obrigatórias para cavalos?','As vacinas essenciais para equinos incluem: raiva (obrigatória por lei), influenza equina, encefalomielite (Leste e Oeste), tétano e herpesvírus equino. O calendário vacinal deve ser adaptado à região e atividade do animal. Consulte sua veterinária para o protocolo ideal.',3],
            ['Aves e Animais Silvestres','fas fa-dove','Posso ter um animal silvestre como pet?','Sim, desde que o animal seja adquirido em criadouros ou estabelecimentos comerciais autorizados pelo IBAMA, com nota fiscal e documentação legal. É crime criar animais silvestres sem autorização. Em caso de encontrar um animal silvestre ferido, entre em contato com o IBAMA ou a polícia ambiental.',1],
            ['Aves e Animais Silvestres','fas fa-dove','Quais cuidados devo ter com aves domésticas?','Aves precisam de alimentação balanceada (ração específica + frutas e legumes), gaiola ou viveiro limpo e espaçoso, água fresca diária, exposição controlada ao sol e enriquecimento ambiental com brinquedos e poleiros variados. Consultas veterinárias periódicas são essenciais para detectar doenças respiratórias e deficiências nutricionais.',2],
            ['Emergência e Urgência','fas fa-ambulance','Meu animal ingeriu algo tóxico, o que fazer?','<strong>NÃO induza o vômito sem orientação veterinária!</strong> Alguns produtos podem causar mais danos ao voltar. Anote o que o animal ingeriu, a quantidade aproximada e o horário. Entre em contato imediatamente com um veterinário. Substâncias comuns perigosas incluem: chocolate, cebola, uvas, medicamentos humanos, produtos de limpeza e raticidas.',1],
            ['Emergência e Urgência','fas fa-ambulance','Como fazer um torniquete improvável em caso de hemorragia?','Em caso de sangramento intenso, aplique pressão direta sobre o ferimento com um pano limpo. Mantenha a pressão por pelo menos 5 minutos sem remover. NÃO use torniquete caseiro, pois pode piorar a situação. Mantenha o animal calmo e transporte-o imediatamente ao veterinário mais próximo.',2],
            ['Emergência e Urgência','fas fa-ambulance','Quais sinais indicam que meu animal precisa de atendimento urgente?','Procure atendimento imediato se observar: dificuldade para respirar, convulsões, vômitos ou diarreia persistentes, sangramento que não para, abdômen inchado e dolorido, incapacidade de urinar, trauma grave (atropelamento, queda), ingestão de corpo estranho ou substâncias tóxicas. Em qualquer dúvida, ligue para o veterinário!',3],
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
        
        $msg = '✅ Todos os dados foram inseridos com sucesso!<br><br>
        <strong>Resumo:</strong><br>
        ✓ Configurações do site (hero, sobre, contato, rodapé)<br>
        ✓ 6 Serviços<br>
        ✓ 4 Membros da Equipe<br>
        ✓ 6 Depoimentos<br>
        ✓ 4 Artigos de Blog<br>
        ✓ 11 Perguntas FAQ<br>
        ✓ 14 Especialidades<br>
        ✓ 8 Clientes parceiros<br><br>
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





