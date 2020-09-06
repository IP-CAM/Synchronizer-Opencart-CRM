<?php

//hero la libreria para hecer uso de sus metodos y poder obtener una imgen correspondiente al CRM
//esta libreria es la misma que usa el CRM para las imagenes 
//se adapto para usarlo con el sincronizador
class system_settings extends CI_Upload
{
    public function __construct()
    {

        parent::__construct();
        
        $this->upload_path        =  to.'/';
        $this->thumbs_path        = to.'/'.'thumbs/';
        $this->image_types        = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif';
        $this->allowed_file_size  = '1024';
        $this->photo = '';
        
    }

    public function add_img($image,$rdir)
    {
			//si el tamaño del File es mayor entonces hay una imagen
            if ($_FILES['userfile']['size']  > 0) {
			
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = 800;
                $config['max_height']    = 800;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                
                //uso de metodos de la libreria

                $this->initialize($config);     
                $this->do_upload();     
				//obtengo el nombre generado por la libreri
                $this->photo = $this->file_name;
				
				
                //lo copio en el ruta de  destino
                copy($rdir.'/'.$_FILES['userfile']['name'], to.'/'.$this->photo);

               
				//uso de libreri Image_lib.php : estaa libreri comprime las imagenes a partir del nombre de la
                //imgen generada
		        $data['image'] = $this->photo;
                $config['image_library']  = 'gd2';
                $config['source_image']   = $this->upload_path . $this->photo;
                $config['new_image']      = $this->thumbs_path . $this->photo;
                $config['maintain_ratio'] = true;
                $config['width']          = 150;
                $config['height']         = 150;
                
               
                
                $this->image_lib = new CI_Image_lib();
                
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
              
                $this->image_lib->clear();
                $config = null;
            }

            return  $this->photo;


    }
    
}

