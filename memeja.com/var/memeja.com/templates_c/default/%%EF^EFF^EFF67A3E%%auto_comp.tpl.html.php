<?php /* Smarty version 2.6.7, created on 2011-09-10 12:56:35
         compiled from leaderboard/auto_comp.tpl.html */ ?>
<?php $this->_foreach['f1'] = array('total' => count($_from = (array)$this->_tpl_vars['sm']['res']), 'iteration' => 0);
if ($this->_foreach['f1']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['i']):
        $this->_foreach['f1']['iteration']++;
 $this->_foreach['f2'] = array('total' => count($_from = (array)$this->_tpl_vars['i']), 'iteration' => 0);
if ($this->_foreach['f2']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['k1'] => $this->_tpl_vars['i1']):
        $this->_foreach['f2']['iteration']++;
 echo $this->_tpl_vars['i1']; ?>

<?php endforeach; endif; unset($_from);  endforeach; endif; unset($_from); ?>